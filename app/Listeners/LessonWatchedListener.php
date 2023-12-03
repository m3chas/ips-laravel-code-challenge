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

        // Check if this is the first lesson watched
        if ($user->lessons->count() == 1) {
            // Find or create the achievement
            $achievement = Achievement::firstOrCreate(['name' => 'First Lesson Watched']);
            
            // Unlock this achievement for the user
            $user->achievements()->attach($achievement);
        }
    }
}
