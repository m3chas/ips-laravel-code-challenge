<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Achievement;
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
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Lesson Watched','required_count' => 1]);
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
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Lesson Watched', 'required_count' => 1]);
        Achievement::factory()->create(['name' => '5 Lessons Watched', 'required_count' => 5]);
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

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    public function testUserUnlocksTenLessonsWatchedAchievement()
    {
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Lesson Watched', 'required_count' => 1]);
        Achievement::factory()->create(['name' => '5 Lessons Watched', 'required_count' => 5]);
        Achievement::factory()->create(['name' => '10 Lessons Watched', 'required_count' => 10]);
        $user = User::factory()->create();  
        $lessons = Lesson::factory()->count(10)->create();

        foreach ($lessons as $lesson) {
            // Update the pivot table to reflect that the lesson is watched
            // I made this way due the challenge rule to avoid any logic on the LessonWatched/CommentWritten events. 
            $user->watched()->attach($lesson, ['watched' => true]);
            $user->refresh();
            
            // Dispatch the LessonWatched event
            event(new LessonWatched($lesson, $user));
        }

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    public function testUserUnlocksTwentyFiveLessonsWatchedAchievement()
    {
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Lesson Watched', 'required_count' => 1]);
        Achievement::factory()->create(['name' => '5 Lessons Watched', 'required_count' => 5]);
        Achievement::factory()->create(['name' => '10 Lessons Watched', 'required_count' => 10]);
        Achievement::factory()->create(['name' => '25 Lessons Watched', 'required_count' => 25]);
        $user = User::factory()->create();  
        $lessons = Lesson::factory()->count(25)->create();

        foreach ($lessons as $lesson) {
            // Update the pivot table to reflect that the lesson is watched
            // I made this way due the challenge rule to avoid any logic on the LessonWatched/CommentWritten events. 
            $user->watched()->attach($lesson, ['watched' => true]);
            $user->refresh();
            
            // Dispatch the LessonWatched event
            event(new LessonWatched($lesson, $user));
        }

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched', '25 Lessons Watched'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    public function testUserUnlocksFiftyLessonsWatchedAchievement()
    {
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Lesson Watched', 'required_count' => 1]);
        Achievement::factory()->create(['name' => '5 Lessons Watched', 'required_count' => 5]);
        Achievement::factory()->create(['name' => '10 Lessons Watched', 'required_count' => 10]);
        Achievement::factory()->create(['name' => '25 Lessons Watched', 'required_count' => 25]);
        Achievement::factory()->create(['name' => '50 Lessons Watched', 'required_count' => 50]);
        $user = User::factory()->create();  
        $lessons = Lesson::factory()->count(50)->create();

        foreach ($lessons as $lesson) {
            // Update the pivot table to reflect that the lesson is watched
            // I made this way due the challenge rule to avoid any logic on the LessonWatched/CommentWritten events. 
            $user->watched()->attach($lesson, ['watched' => true]);
            $user->refresh();
            
            // Dispatch the LessonWatched event
            event(new LessonWatched($lesson, $user));
        }

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched', '25 Lessons Watched', '50 Lessons Watched'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    public function testUserUnlocksFirstCommentWrittenAchievement()
    {
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Comment Written', 'required_count' => 1, 'type' => 'comment_written']);
        $user = User::factory()->create();  

        // Simulate a comment made by the new user.
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $user->refresh();

        // Simulate CommentWritten event dispatch
        event(new CommentWritten($comment, $user));

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written'],
                'next_available_achievements' => ['3 Comments Written'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }
}