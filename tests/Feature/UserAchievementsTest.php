<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Events\LessonWatched;
use Illuminate\Support\Facades\Log;

class UserAchievementsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to ensure a user unlocks the 'First Lesson Watched' achievement 
     * after watching their first lesson.
     */
    public function testUserUnlocksFirstLessonWatchedAchievement()
    {
        // Create a user and lesson
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        // Update the pivot table to reflect that the lesson is watched
        // I made this way due the challenge rule to avoid any logic on the LessonWatched/CommentWritten events. 
        $user->watched()->attach($lesson, ['watched' => true]);
        $user->refresh();

        // Dispatch the LessonWatched event
        event(new LessonWatched($lesson, $user));

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
                 ->assertJson([
                     'unlocked_achievements' => ['First Lesson Watched'],
                     'next_available_achievements' => ['5 Lessons Watched'],
                     'current_badge' => '',
                     'next_badge' => '',
                     'remaining_to_unlock_next_badge' => 0
                ]);
    }

    /**
     * Test to ensure a user unlocks the '5 Lessons Watched' achievement 
     * after watching their 5 lessons.
     */
    public function testUserUnlocksFiveLessonsWatchedAchievement()
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();

        foreach ($lessons as $lesson) {
            // Update the pivot table to reflect that the lesson is watched
            // I made this way due the challenge rule to avoid any logic on the LessonWatched/CommentWritten events. 
            $user->watched()->attach($lesson, ['watched' => true]);
            $user->refresh();
            
            // Dispatch the LessonWatched event
            event(new LessonWatched($lesson, $user));
        }

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
                ->assertJson([
                    'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched'],
                    // ... other expected fields
                ]);
    }

}