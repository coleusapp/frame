<?php

namespace Coleus\Frame\Http\Resources;

use Coleus\Frame\Contracts\ArtworkSource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ArtworkSource */
class ArtworkSourceResource extends JsonResource
{
    /** @return array{label: string, value: string} */
    public function toArray(Request $request): array
    {
        return [
            'label' => $this->name(),
            'value' => $this->key(),
        ];
    }
}
