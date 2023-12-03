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
        if ($user->watched()->count() == 1) {
            $this->unlockAchievement('First Lesson Watched', $user);
        }

        // Check for "5 Lessons Watched" achievement
        if ($user->watched()->count() == 5) {
            $this->unlockAchievement('5 Lessons Watched', $user);
        }
    }

    /**
     * Unlock a specific achievement for the user.
     *
     * @param string $achievementName
     * @param \App\Models\User $user
     */
    private function unlockAchievement($achievementName, $user)
    {
        $achievement = Achievement::firstOrCreate(['name' => $achievementName]);
        $user->achievements()->attach($achievement);
    }
}
