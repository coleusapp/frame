<?php

namespace Coleus\Frame\Http\Controllers;

use App\Http\Controllers\Controller;
use Coleus\Frame\Facades\Frames;
use Coleus\Frame\Models\Frame;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreviewArtworkController extends Controller
{
    public function show(Request $request, Frame $frame): JsonResponse|Response
    {
        $artwork = Frames::kiosk()->preview($frame, $request->integer('current') ?: null);

        if (!$artwork) {
            return response()->noContent();
        }

        return response()->json(Frames::kiosk()->payload(
            $frame,
            $artwork,
            route('frame.preview.image', ['frame' => $frame, 'artwork' => $artwork->id], false),
        ));
    }
}
