<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
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
        $user->load('comments');
        $commentCount = $user->comments()->count();

        // Fetch and unlock achievements matching the exact comment count
        $achievement = Achievement::where('required_count', $commentCount)
                                  ->where('type', 'comment_written')
                                  ->first();
        // I've included a validation if the user already have this achievement unlocked.
        if ($achievement && !$user->achievements->contains($achievement->id)) {
            $oldBadge = $user->badge;
            $user->achievements()->attach($achievement);

            // Dispatch the AchievementUnlocked with achievement name
            event(new AchievementUnlocked($achievement->name, $user));

            // Recalculate the badge after attaching the new achievement
            $newBadge = $user->calculateBadge();
            if ($newBadge !== $oldBadge) {
                // Dispatch the BadgeUnlocked event if the badge has changed
                event(new BadgeUnlocked($newBadge, $user));
            }
        }
    }
}
