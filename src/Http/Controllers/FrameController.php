<?php

namespace Coleus\Frame\Http\Controllers;

use App\Http\Controllers\Controller;
use Coleus\Frame\Facades\Frames;
use Coleus\Frame\Http\Requests\FrameRequest;
use Coleus\Frame\Http\Resources\ArtworkSourceResource;
use Coleus\Frame\Http\Resources\FrameResource;
use Coleus\Frame\Models\Frame;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FrameController extends Controller
{
    public const string TOKEN_FLASH_KEY = 'frame.kiosk_token';

    public function index(): Response
    {
        return Inertia::render('frames/Index', [
            'collection' => FrameResource::collection(Frames::frame()->index()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('frames/Create', [
            'resource' => FrameResource::make(Frames::frame()->default()),
            'sources' => ArtworkSourceResource::collection(Frames::artwork()->sources()),
        ]);
    }

    public function store(FrameRequest $request): RedirectResponse
    {
        $kioskToken = Frames::frame()->create($request->validated());

        return to_route('frame.edit', ['frame' => $kioskToken->frame])->with(self::TOKEN_FLASH_KEY, $kioskToken->token);
    }

    public function edit(Frame $frame): Response
    {
        $token = session(self::TOKEN_FLASH_KEY);

        return Inertia::render('frames/Edit', [
            'resource' => FrameResource::make(Frames::frame()->show($frame)),
            'sources' => ArtworkSourceResource::collection(Frames::artwork()->sources()),
            'kiosk_url' => $token ? route('frame.kiosk.show', ['token' => $token]) : null,
        ]);
    }

    public function update(FrameRequest $request, Frame $frame): RedirectResponse
    {
        Frames::frame()->update($frame, $request->validated());

        return back();
    }

    public function destroy(Frame $frame): RedirectResponse
    {
        Frames::frame()->destroy($frame);

        return to_route('frame.index');
    }

    public function regenerateToken(Frame $frame): RedirectResponse
    {
        $kioskToken = Frames::frame()->regenerateToken($frame);

        return to_route('frame.edit', ['frame' => $frame])->with(self::TOKEN_FLASH_KEY, $kioskToken->token);
    }

    public function fetch(Frame $frame): RedirectResponse
    {
        Frames::artwork()->queueFetch($frame);

        return back();
    }
}
