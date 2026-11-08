<?php

namespace Coleus\Frame\Models;

use Coleus\Frame\Database\Factories\FrameFactory;
use Coleus\Frame\Enums\CaptionPosition;
use Coleus\Frame\FrameModelDefaults;
use Coleus\Users\Concerns\HasUser;
use Coleus\Users\Models\Scopes\UserScope;
use Coleus\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $token_hash
 * @property string|null $search_query
 * @property list<string> $sources ArtworkSource::key() of each source the frame fetches from.
 * @property int $interval_seconds
 * @property int $display_width Largest size, with display_height, the frame's artworks are stored at.
 * @property int $display_height
 * @property int $rotation Clockwise degrees the kiosk page is turned (0, 90, 180 or 270), for a screen hung on its side.
 * @property int $inset_top Pixels the physical frame hides at the top of the screen; likewise for the other insets.
 * @property int $inset_right
 * @property int $inset_bottom
 * @property int $inset_left
 * @property string $background_color Hex colour of the kiosk page around the art, e.g. #000000.
 * @property bool $show_caption
 * @property CaptionPosition $caption_position
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $artworks_count
 * @property-read Collection<int, FrameArtwork> $artworks
 * @property-read Collection<int, User> $users
 *
 * @mixin \Eloquent
 */
#[ScopedBy([UserScope::class])]
class Frame extends FrameModelDefaults
{
    /** @uses \Coleus\Frame\Database\Factories\FrameFactory */
    use HasFactory;

    use HasUser;

    protected $fillable = [
        'name',
        'search_query',
        'sources',
        'interval_seconds',
        'display_width',
        'display_height',
        'rotation',
        'inset_top',
        'inset_right',
        'inset_bottom',
        'inset_left',
        'background_color',
        'show_caption',
        'caption_position',
        'is_active',
    ];

    protected $attributes = [
        'interval_seconds' => 60,
        'rotation' => 0,
        'inset_top' => 0,
        'inset_right' => 0,
        'inset_bottom' => 0,
        'inset_left' => 0,
        'background_color' => '#000000',
        'show_caption' => true,
        'caption_position' => 'bottom-left',
        'is_active' => true,
    ];

    public function casts(): array
    {
        return [
            'sources' => 'array',
            'interval_seconds' => 'integer',
            'display_width' => 'integer',
            'display_height' => 'integer',
            'rotation' => 'integer',
            'inset_top' => 'integer',
            'inset_right' => 'integer',
            'inset_bottom' => 'integer',
            'inset_left' => 'integer',
            'show_caption' => 'boolean',
            'caption_position' => CaptionPosition::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<FrameArtwork, $this>
     */
    public function artworks(): HasMany
    {
        return $this->hasMany(FrameArtwork::class);
    }

    protected static function newFactory(): FrameFactory|Factory
    {
        return FrameFactory::new();
    }
}
