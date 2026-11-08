<?php

namespace Coleus\Frame\Data;

use Spatie\LaravelData\Data;

class FrameData extends Data
{
    /** @param  list<string>  $sources */
    public function __construct(
        public string $name,
        public ?string $search_query,
        public array $sources,
        public int $interval_seconds,
        public int $display_width,
        public int $display_height,
        public int $rotation,
        public int $inset_top,
        public int $inset_right,
        public int $inset_bottom,
        public int $inset_left,
        public string $background_color,
        public bool $show_caption,
        public bool $is_active,
    ) {
        //
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $searchQuery = trim((string) ($properties['search_query'] ?? ''));

        return [
            ...$properties,
            'search_query' => filled($searchQuery) ? $searchQuery : null,
            'display_width' => $properties['display_width'] ?? config('frame.display.width'),
            'display_height' => $properties['display_height'] ?? config('frame.display.height'),
            'rotation' => $properties['rotation'] ?? 0,
            'inset_top' => $properties['inset_top'] ?? 0,
            'inset_right' => $properties['inset_right'] ?? 0,
            'inset_bottom' => $properties['inset_bottom'] ?? 0,
            'inset_left' => $properties['inset_left'] ?? 0,
            'background_color' => strtolower($properties['background_color'] ?? '#000000'),
            'show_caption' => filter_var($properties['show_caption'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_active' => filter_var($properties['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
