<?php

namespace Coleus\Frame\Http\Requests;

use Coleus\Frame\Enums\CaptionPosition;
use Coleus\Frame\Facades\Frames;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrameRequest extends FormRequest
{
    public const int MIN_INTERVAL_SECONDS = 10;

    public const int MAX_INTERVAL_SECONDS = 86_400;

    public const int MIN_DISPLAY_SIZE = 320;

    public const int MAX_DISPLAY_SIZE = 7680;

    public const array ROTATIONS = [0, 90, 180, 270];

    public const int MAX_INSET = 1000;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'search_query' => ['nullable', 'string', 'max:255'],
            'sources' => ['required', 'array', 'min:1'],
            'sources.*' => ['required', 'string', 'distinct', Rule::in(Frames::artwork()->sourceKeys())],
            'interval_seconds' => ['required', 'integer', 'min:'.self::MIN_INTERVAL_SECONDS, 'max:'.self::MAX_INTERVAL_SECONDS],
            'display_width' => ['nullable', 'integer', 'min:'.self::MIN_DISPLAY_SIZE, 'max:'.self::MAX_DISPLAY_SIZE],
            'display_height' => ['nullable', 'integer', 'min:'.self::MIN_DISPLAY_SIZE, 'max:'.self::MAX_DISPLAY_SIZE],
            'rotation' => ['nullable', 'integer', Rule::in(self::ROTATIONS)],
            'inset_top' => ['nullable', 'integer', 'min:0', 'max:'.self::MAX_INSET],
            'inset_right' => ['nullable', 'integer', 'min:0', 'max:'.self::MAX_INSET],
            'inset_bottom' => ['nullable', 'integer', 'min:0', 'max:'.self::MAX_INSET],
            'inset_left' => ['nullable', 'integer', 'min:0', 'max:'.self::MAX_INSET],
            'background_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'show_caption' => ['nullable', 'boolean'],
            'caption_position' => ['nullable', Rule::enum(CaptionPosition::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Give the frame a name.',
            'sources.required' => 'Pick at least one source.',
            'sources.min' => 'Pick at least one source.',
            'sources.*.in' => 'Pick sources from the list.',
            'rotation.in' => 'Pick a rotation from the list.',
            'caption_position.enum' => 'Pick a caption position from the list.',
            'background_color.regex' => 'Pick a colour like #000000.',
            'interval_seconds.min' => 'Show each artwork for at least '.self::MIN_INTERVAL_SECONDS.' seconds.',
            'interval_seconds.max' => 'Show each artwork for at most a day ('.self::MAX_INTERVAL_SECONDS.' seconds).',
        ];
    }
}
