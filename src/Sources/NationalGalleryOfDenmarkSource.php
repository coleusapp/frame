<?php

namespace Coleus\Frame\Sources;

use Coleus\Frame\Contracts\ArtworkSource;
use Coleus\Frame\Data\ArtworkData;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * Public domain paintings from SMK, the National Gallery of Denmark. Images
 * come from its IIIF server, bounded to MaxImageSize so huge scans aren't
 * downloaded in full; a result without IIIF falls back to the 1024 px thumbnail.
 */
class NationalGalleryOfDenmarkSource implements ArtworkSource
{
    public const int MaxImageSize = 3000;

    public function key(): string
    {
        return 'smk';
    }

    public function name(): string
    {
        return 'SMK – National Gallery of Denmark';
    }

    /**
     * @return Collection<int, ArtworkData>
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function search(?string $query, int $count): Collection
    {
        $found = $this->searchPage($query, offset: 0, rows: 1)['found'] ?? 0;

        if (!$found) {
            return collect();
        }

        $offset = random_int(0, max(0, $found - $count));

        return collect($this->searchPage($query, $offset, $count)['items'] ?? [])
            ->map(fn (array $item) => [...$item, 'image_url' => $this->imageUrl($item)])
            ->filter(fn (array $item) => filled($item['image_url']))
            ->map(fn (array $item) => new ArtworkData(
                source_id: (int) $item['id'],
                title: ($item['titles'][0]['title'] ?? null) ?: 'Untitled',
                artist_display: $item['production'][0]['creator'] ?? null,
                date_display: $item['production_date'][0]['period'] ?? null,
                image_url: $item['image_url'],
            ))
            ->values();
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function image(ArtworkData $artwork): string
    {
        return $this->client()
            ->accept('image/*')
            ->get($artwork->image_url)
            ->throw()
            ->body();
    }

    /** @param  array{image_iiif_id?: ?string, image_thumbnail?: ?string}  $item */
    private function imageUrl(array $item): ?string
    {
        if (filled($item['image_iiif_id'] ?? null)) {
            $size = self::MaxImageSize;

            return "{$item['image_iiif_id']}/full/!{$size},{$size}/0/default.jpg";
        }

        return $item['image_thumbnail'] ?? null;
    }

    /**
     * @return array{found?: int, items?: list<array{id: string, titles?: list<array{title: ?string}>, production?: list<array{creator: ?string}>, production_date?: list<array{period: ?string}>, image_iiif_id?: ?string, image_thumbnail?: ?string}>}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    private function searchPage(?string $query, int $offset, int $rows): array
    {
        return $this->client()
            ->get(config('frame.smk.api_url').'/art/search/', [
                'keys' => filled($query) ? $query : '*',
                'filters' => '[public_domain:true],[has_image:true],[object_names:painting]',
                'lang' => 'en',
                'offset' => $offset,
                'rows' => $rows,
            ])
            ->throw()
            ->json();
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->connectTimeout(10)
            ->timeout(60);
    }
}
