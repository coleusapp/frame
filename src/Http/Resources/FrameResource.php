<?php

namespace Coleus\Frame\Http\Resources;

use Coleus\Frame\Models\Frame;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Frame */
class FrameResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'search_query' => $this->search_query,
            'sources' => $this->sources,
            'interval_seconds' => $this->interval_seconds,
            'display_width' => $this->display_width,
            'display_height' => $this->display_height,
            'rotation' => $this->rotation,
            'inset_top' => $this->inset_top,
            'inset_right' => $this->inset_right,
            'inset_bottom' => $this->inset_bottom,
            'inset_left' => $this->inset_left,
            'background_color' => $this->background_color,
            'show_caption' => $this->show_caption,
            'caption_position' => $this->caption_position,
            'is_active' => $this->is_active,
            'artworks_count' => $this->artworks_count ?? 0,
        ];
    }
}
