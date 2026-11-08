<?php

namespace Coleus\Frame\Sources;

use Coleus\Frame\Contracts\ArtworkSource;
use Coleus\Frame\Data\ArtworkData;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Public domain paintings from the Rijksmuseum Data Services (no key). The
 * search only returns object ids, 100 per page behind a `next` cursor, so a
 * random page is reached by following the cursor, and each result costs one
 * more request for its Dublin Core record (title, creator, date, rights and
 * IIIF image). It has no free-text search; the query matches descriptions.
 */
class RijksmuseumSource implements ArtworkSource
{
    public const int PageSize = 100;

    public const int MaxImageSize = 3000;

    /** @var list<string> */
    public const array PublicDomainRights = [
        'https://creativecommons.org/publicdomain/mark/1.0/',
        'https://creativecommons.org/publicdomain/zero/1.0/',
    ];

    public function key(): string
    {
        return 'rijks';
    }

    public function name(): string
    {
        return 'Rijksmuseum';
    }

    /**
     * @return Collection<int, ArtworkData>
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function search(?string $query, int $count): Collection
    {
        $page = $this->randomPage($query);

        return collect($page['orderedItems'] ?? [])
            ->pluck('id')
            ->shuffle()
            ->take($count)
            ->map(fn (string $objectUrl) => $this->record($objectUrl))
            ->filter()
            ->filter(fn (array $record) => $this->isPublicDomain($record))
            ->map(fn (array $record) => [...$record, 'image_url' => $this->imageUrl($record)])
            ->filter(fn (array $record) => filled($record['image_url']))
            ->map(fn (array $record) => new ArtworkData(
                source_id: (int) Str::afterLast($record['@id'], '/'),
                title: $this->text($record['title'] ?? null) ?: 'Untitled',
                artist_display: $this->text($this->first($record['creator'] ?? null)['title'] ?? null),
                date_display: $this->text($record['date'] ?? null),
                image_url: $record['image_url'],
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

    /**
     * @return array{partOf?: array{totalItems?: int}, next?: array{id: string}, orderedItems?: list<array{id: string}>}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    private function randomPage(?string $query): array
    {
        $parameters = ['type' => 'painting', 'imageAvailable' => 'true'];

        if (filled($query)) {
            $parameters['description'] = $query;
        }

        $page = $this->client()
            ->get(config('frame.rijks.search_url'), $parameters)
            ->throw()
            ->json();

        $pages = (int) ceil(($page['partOf']['totalItems'] ?? 0) / self::PageSize);

        $steps = random_int(0, max(0, $pages - 1));

        for ($step = 0; $step < $steps && isset($page['next']['id']); $step++) {
            $page = $this->client()
                ->get($page['next']['id'])
                ->throw()
                ->json();
        }

        return $page;
    }

    /**
     * A missing or failing record is skipped rather than failing the search.
     *
     * @return ?array<string, mixed>
     *
     * @throws ConnectionException
     */
    private function record(string $objectUrl): ?array
    {
        $response = $this->client()
            ->accept('application/ld+json')
            ->get($objectUrl, ['_profile' => 'dc', '_mediatype' => 'application/ld+json']);

        return $response->successful() ? $response->json() : null;
    }

    /** @param  array<string, mixed>  $record */
    private function isPublicDomain(array $record): bool
    {
        return in_array($this->first($record['rights'] ?? null)['@id'] ?? null, self::PublicDomainRights, true);
    }

    /** @param  array<string, mixed>  $record */
    private function imageUrl(array $record): ?string
    {
        $url = $this->first($record['relation'] ?? null)['@id'] ?? null;

        if (!$url || !str_contains($url, '/full/max/')) {
            return $url;
        }

        $size = self::MaxImageSize;

        return str_replace('/full/max/', "/full/!{$size},{$size}/", $url);
    }

    /**
     * A Dublin Core value is a string, a list of {@language, @value}, or a
     * list of either; the English value is preferred.
     */
    private function text(mixed $value): ?string
    {
        if (is_string($value) || !$value) {
            return $value ?: null;
        }

        $values = Arr::isList($value) ? $value : [$value];

        $english = Arr::first($values, fn (mixed $item) => is_array($item) && ($item['@language'] ?? null) === 'en');

        $chosen = $english ?? $values[0];

        return is_array($chosen) ? ($chosen['@value'] ?? null) : $chosen;
    }

    /** One Dublin Core field may hold a single object or a list of them. */
    private function first(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        return Arr::isList($value) ? ($value[0] ?? null) : $value;
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->connectTimeout(10)
            ->timeout(60);
    }
}
