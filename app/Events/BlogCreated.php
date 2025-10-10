<?php

namespace App\Events;

use App\Models\Blog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BlogCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }
}