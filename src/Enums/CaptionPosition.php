<?php

namespace Coleus\Frame\Enums;

/** Where the caption sits inside the kiosk's visible area; the value is the kiosk's `position-<value>` CSS class. */
enum CaptionPosition: string
{
    case TopLeft = 'top-left';
    case TopCenter = 'top-center';
    case TopRight = 'top-right';
    case MiddleLeft = 'middle-left';
    case MiddleRight = 'middle-right';
    case BottomLeft = 'bottom-left';
    case BottomCenter = 'bottom-center';
    case BottomRight = 'bottom-right';
}
