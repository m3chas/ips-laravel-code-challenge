<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Models\Achievement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event): void
    {
        $user = $event->comment->user;
        $commentCount = $user->comments()->count();

        // Fetch and unlock achievements matching the exact comment count
        $achievement = Achievement::where('required_count', $commentCount)
                                  ->where('type', 'comment_written')
                                  ->first();
        // I've included a validation if the user already have this achievement unlocked.
        if ($achievement && !$user->achievements->contains($achievement->id)) {
            $user->achievements()->attach($achievement);
        }
    }
}
