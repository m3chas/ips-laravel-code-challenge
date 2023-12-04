<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Models\Achievement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonWatchedListener
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
     *
     * @param  LessonWatched  $event
     * @return void
     */
    public function handle(LessonWatched $event)
    {
        $user = $event->user;
        $watchedCount = $user->watched()->count();

        // Fetch and unlock achievements matching the exact watched lessons count
        $achievement = Achievement::where('required_count', $watchedCount)
                                  ->where('type', 'lesson_watched')
                                  ->first();
        // I've included a validation if the user already have this achievement unlocked.
        if ($achievement && !$user->achievements->contains($achievement->id)) {
            $user->achievements()->attach($achievement);
        }
    }
}
