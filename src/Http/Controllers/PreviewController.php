<?php

namespace Coleus\Frame\Http\Controllers;

use App\Http\Controllers\Controller;
use Coleus\Frame\Facades\Frames;
use Coleus\Frame\Models\Frame;
use Illuminate\Contracts\View\View;

/**
 * The kiosk page for the signed-in owner, without the frame's token. It
 * works for inactive frames too, and can later be embedded in the edit page.
 */
class PreviewController extends Controller
{
    public function show(Frame $frame): View
    {
        return view('frame::kiosk', Frames::kiosk()->page($frame, route('frame.preview.next', ['frame' => $frame], false)));
    }
}
