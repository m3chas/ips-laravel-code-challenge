<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Events\LessonWatched;

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

        // Dispatch the LessonWatched event
        event(new LessonWatched($lesson, $user));

        // Refresh the user model to get the updated state
        $user->refresh();

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
}