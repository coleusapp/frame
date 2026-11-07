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
            'is_active' => $this->is_active,
            'artworks_count' => $this->artworks_count ?? 0,
        ];
    }
}
