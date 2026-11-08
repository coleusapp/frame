<?php

namespace Coleus\Frame\Services;

use Coleus\Frame\Models\Frame;
use Coleus\Frame\Models\FrameArtwork;
use Coleus\Users\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KioskService
{
    public const int RotationWindow = 20;

    public function generateToken(): string
    {
        return Str::random(48);
    }

    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function frame(string $token): ?Frame
    {
        return Frame::query()
            ->withoutGlobalScope(UserScope::class)
            ->where('token_hash', $this->hashToken($token))
            ->where('is_active', true)
            ->first();
    }

    /** @return array{nextUrl: string, inset: array{top: int, right: int, bottom: int, left: int}, backgroundColor: string, rotation: int} */
    public function page(Frame $frame, string $nextUrl): array
    {
        return [
            'nextUrl' => $nextUrl,
            'inset' => $this->inset($frame),
            'backgroundColor' => $frame->background_color,
            'rotation' => $frame->rotation,
        ];
    }

    /** @return array<string, mixed> */
    public function payload(Frame $frame, FrameArtwork $artwork, string $imageUrl): array
    {
        return [
            'id' => $artwork->id,
            'image_url' => $imageUrl,
            'title' => $artwork->title,
            'artist' => $artwork->artist_display,
            'date' => $artwork->date_display,
            'interval_seconds' => $frame->interval_seconds,
            'inset' => $this->inset($frame),
            'background_color' => $frame->background_color,
            'show_caption' => $frame->show_caption,
            'caption_position' => $frame->caption_position,
            'rotation' => $frame->rotation,
        ];
    }

    /** @return array{top: int, right: int, bottom: int, left: int} */
    public function inset(Frame $frame): array
    {
        return [
            'top' => $frame->inset_top,
            'right' => $frame->inset_right,
            'bottom' => $frame->inset_bottom,
            'left' => $frame->inset_left,
        ];
    }

    public function next(Frame $frame, ?int $currentArtworkId): ?FrameArtwork
    {
        $artwork = $this->preview($frame, $currentArtworkId);

        $artwork?->forceFill(['last_shown_at' => now()])->save();

        return $artwork;
    }

    /** Picks the next artwork like next(), without counting it as shown, so previewing leaves the frame's rotation alone. */
    public function preview(Frame $frame, ?int $currentArtworkId): ?FrameArtwork
    {
        return $this->pick($frame, $currentArtworkId) ?? ($currentArtworkId ? $this->pick($frame, null) : null);
    }

    public function artwork(Frame $frame, int $artworkId): ?FrameArtwork
    {
        $artwork = $frame->artworks()->find($artworkId);

        if (!$artwork || !Storage::disk(config('frame.disk'))->exists($artwork->path)) {
            return null;
        }

        return $artwork;
    }

    public function imageResponse(FrameArtwork $artwork): StreamedResponse
    {
        return Storage::disk(config('frame.disk'))->response($artwork->path, null, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, max-age=31536000, immutable',
        ]);
    }

    private function pick(Frame $frame, ?int $excludedArtworkId): ?FrameArtwork
    {
        $candidates = $frame->artworks()
            ->when($excludedArtworkId, fn (Builder $query) => $query->whereKeyNot($excludedArtworkId))
            ->orderByRaw('case when last_shown_at is null then 0 else 1 end')
            ->orderBy('last_shown_at')
            ->orderBy('id')
            ->limit(self::RotationWindow)
            ->get();

        return $candidates->isEmpty() ? null : $candidates->random();
    }
}
