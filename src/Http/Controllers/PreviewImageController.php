<?php

namespace Coleus\Frame\Http\Controllers;

use App\Http\Controllers\Controller;
use Coleus\Frame\Facades\Frames;
use Coleus\Frame\Models\Frame;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreviewImageController extends Controller
{
    public function show(Frame $frame, int $artwork): StreamedResponse
    {
        $artwork = Frames::kiosk()->artwork($frame, $artwork) ?? abort(404);

        return Frames::kiosk()->imageResponse($artwork);
    }
}
