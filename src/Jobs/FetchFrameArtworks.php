<?php

namespace Coleus\Frame\Jobs;

use Coleus\Frame\Facades\Frames;
use Coleus\Frame\Models\Frame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class FetchFrameArtworks implements NotTenantAware, ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 900;

    public int $uniqueFor = 900;

    public function __construct(
        public Frame $frame,
        public int $count = 20,
        public ?int $replaceUpTo = null,
    ) {}

    /** A replacement has its own key, so a plain fetch already queued for the frame can't swallow it. */
    public function uniqueId(): string
    {
        return $this->replaceUpTo ? "{$this->frame->id}-replace" : (string) $this->frame->id;
    }

    public function handle(): void
    {
        Frames::artwork()->fetch($this->frame, $this->count, $this->replaceUpTo);
    }
}
