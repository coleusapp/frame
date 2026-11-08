<?php

namespace Coleus\Frame\Services;

use Coleus\Frame\Contracts\ArtworkSource;
use Coleus\Frame\Data\ArtworkData;
use Coleus\Frame\Jobs\FetchFrameArtworks;
use Coleus\Frame\Models\Frame;
use Coleus\Frame\Models\FrameArtwork;
use Coleus\Frame\Support\ImageFitter;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ArtworkService
{
    public function queueFetch(Frame $frame, int $count = 20): void
    {
        FetchFrameArtworks::dispatch($frame, $count);
    }

    /**
     * Queues a fetch that, once it has stored at least one new artwork,
     * deletes every artwork the frame has now, so the frame never goes blank.
     */
    public function queueReplacement(Frame $frame, int $count = 20): void
    {
        FetchFrameArtworks::dispatch($frame, $count, $frame->artworks()->max('id'));
    }

    /** With $replaceUpTo, artworks with that id or lower are deleted if the fetch stored any newer one. */
    public function fetch(Frame $frame, int $count, ?int $replaceUpTo = null): void
    {
        foreach ($this->sourcesOf($frame) as $source) {
            $existingSourceIds = $frame->artworks()
                ->where('source', $source->key())
                ->pluck('source_id')
                ->all();

            $source->search($frame->search_query, $count)
                ->reject(fn (ArtworkData $artwork) => in_array($artwork->source_id, $existingSourceIds, true))
                ->each(fn (ArtworkData $artwork) => $this->store($frame, $source, $artwork));
        }

        if ($replaceUpTo) {
            $this->replace($frame, $replaceUpTo);
        }

        $this->prune($frame);
    }

    /** @return list<string> */
    public function sourceKeys(): array
    {
        return $this->sources()
            ->map(fn (ArtworkSource $source) => $source->key())
            ->all();
    }

    /**
     * Read on every fetch rather than injected, so frame.sources can change at runtime.
     *
     * @return Collection<int, ArtworkSource>
     */
    public function sources(): Collection
    {
        return collect(config('frame.sources'))->map(fn (string $source) => app($source));
    }

    public function directory(Frame $frame): string
    {
        return "frame/{$frame->id}";
    }

    public function deleteFiles(Frame $frame): void
    {
        $this->disk()->deleteDirectory($this->directory($frame));
    }

    private function store(Frame $frame, ArtworkSource $source, ArtworkData $artwork): void
    {
        try {
            $jpeg = ImageFitter::fitAsJpeg(
                $source->image($artwork),
                $frame->display_width,
                $frame->display_height,
            );
        } catch (Throwable $exception) {
            Log::warning('Frame artwork image skipped.', [
                'frame_id' => $frame->id,
                'source' => $source->key(),
                'source_id' => $artwork->source_id,
                'url' => $artwork->image_url,
                'error' => $exception->getMessage(),
            ]);

            return;
        }

        $path = "{$this->directory($frame)}/{$source->key()}/{$artwork->source_id}.jpg";

        $this->disk()->put($path, $jpeg);

        $frame->artworks()->create([
            'source' => $source->key(),
            'source_id' => $artwork->source_id,
            'title' => Str::limit($artwork->title, 255, ''),
            'artist_display' => $this->limitOrNull($artwork->artist_display),
            'date_display' => $this->limitOrNull($artwork->date_display),
            'path' => $path,
        ]);
    }

    private function replace(Frame $frame, int $replaceUpTo): void
    {
        if (!$frame->artworks()->where('id', '>', $replaceUpTo)->exists()) {
            return;
        }

        $this->deleteArtworks($frame->artworks()->where('id', '<=', $replaceUpTo)->get());
    }

    private function prune(Frame $frame): void
    {
        $excess = $frame->artworks()->count() - config('frame.pool_size');

        if ($excess <= 0) {
            return;
        }

        $this->deleteArtworks($frame->artworks()
            ->oldest()
            ->oldest('id')
            ->limit($excess)
            ->get());
    }

    /** @param  EloquentCollection<int, FrameArtwork>  $artworks */
    private function deleteArtworks(EloquentCollection $artworks): void
    {
        $artworks->each(function (FrameArtwork $artwork): void {
            $this->disk()->delete($artwork->path);
            $artwork->delete();
        });
    }

    /**
     * A key no longer in frame.sources is ignored.
     *
     * @return Collection<int, ArtworkSource>
     */
    private function sourcesOf(Frame $frame): Collection
    {
        return $this->sources()
            ->filter(fn (ArtworkSource $source) => in_array($source->key(), $frame->sources, true))
            ->values();
    }

    private function disk(): Filesystem
    {
        return Storage::disk(config('frame.disk'));
    }

    private function limitOrNull(?string $value): ?string
    {
        return blank($value) ? null : Str::limit($value, 255, '');
    }
}
