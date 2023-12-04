<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use App\Models\Badge;
use App\Models\Achievement;
use App\Events\LessonWatched;
use App\Events\CommentWritten;
use Illuminate\Support\Facades\Log;

class UserAchievementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Achievement data
        Achievement::factory()->create(['name' => 'First Lesson Watched', 'required_count' => 1, 'type' => 'lesson_watched']);
        Achievement::factory()->create(['name' => '5 Lessons Watched', 'required_count' => 5, 'type' => 'lesson_watched']);
        Achievement::factory()->create(['name' => '10 Lessons Watched', 'required_count' => 10, 'type' => 'lesson_watched']);
        Achievement::factory()->create(['name' => '25 Lessons Watched', 'required_count' => 25, 'type' => 'lesson_watched']);
        Achievement::factory()->create(['name' => '50 Lessons Watched', 'required_count' => 50, 'type' => 'lesson_watched']);

        Achievement::factory()->create(['name' => 'First Comment Written', 'required_count' => 1, 'type' => 'comment_written']);
        Achievement::factory()->create(['name' => '3 Comments Written', 'required_count' => 3, 'type' => 'comment_written']);
        Achievement::factory()->create(['name' => '5 Comments Written', 'required_count' => 5, 'type' => 'comment_written']);
        Achievement::factory()->create(['name' => '10 Comments Written', 'required_count' => 10, 'type' => 'comment_written']);
        Achievement::factory()->create(['name' => '20 Comments Written', 'required_count' => 20, 'type' => 'comment_written']);

        Badge::factory()->create(['name' => 'Beginner', 'achievement_count' => 0]);
        Badge::factory()->create(['name' => 'Intermediate', 'achievement_count' => 4]);
        Badge::factory()->create(['name' => 'Advanced', 'achievement_count' => 8]);
        Badge::factory()->create(['name' => 'Master', 'achievement_count' => 10]);
    }

    protected function createLessonsAndDispatchEvents($user, $count)
    {
        $lessons = Lesson::factory()->count($count)->create();
        foreach ($lessons as $lesson) {
            // Update the pivot table to reflect that the lesson is watched
            // I made this way due the challenge rule to avoid any logic on the LessonWatched/CommentWritten events. 
            $user->watched()->attach($lesson, ['watched' => true]);
            $user->refresh();
            
            // Dispatch the LessonWatched event
            event(new LessonWatched($lesson, $user));
        }
    }

    protected function createCommentsAndDispatchEvents($user, $count)
    {
        // Simulate a comment made by the new user and dispatch event after each comment.
        for ($i = 0; $i < $count; $i++) {
            $comment = Comment::factory()->create(['user_id' => $user->id]);
            $user->refresh();
            event(new CommentWritten($comment));
        }
    }

    /**
     * Test to ensure a user unlocks the 'First Lesson Watched' achievement 
     * after watching their first lesson.
     */
    public function testUserUnlocksFirstLessonWatchedAchievement()
    {
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Lesson Watched','required_count' => 1, 'type' => 'lesson_watched']);
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
        $user = User::factory()->create();  

        // Dispatch the data and events.
        $this->createLessonsAndDispatchEvents($user, 5);

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

     /**
     * Test to ensure a user unlocks the '10 Lessons Watched' achievement 
     * after watching their 10 lessons.
     */
    public function testUserUnlocksTenLessonsWatchedAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();  
        
        // Dispatch the data and events.
        $this->createLessonsAndDispatchEvents($user, 10);

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

     /**
     * Test to ensure a user unlocks the '25 Lessons Watched' achievement 
     * after watching their 25 lessons.
     */
    public function testUserUnlocksTwentyFiveLessonsWatchedAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();  
        
        // Dispatch the data and events.
        $this->createLessonsAndDispatchEvents($user, 25);

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

    /**
     * Test to ensure a user unlocks the '50 Lessons Watched' achievement 
     * after watching their 50 lessons.
    */
    public function testUserUnlocksFiftyLessonsWatchedAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();  

        // Dispatch the data and events.
        $this->createLessonsAndDispatchEvents($user, 50);

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

    /**
     * Test to ensure a user unlocks the 'First Comment Written' achievement 
     * after their first comment.
    */
    public function testUserUnlocksFirstCommentWrittenAchievement()
    {
        // Generate necesary data for this test.
        Achievement::factory()->create(['name' => 'First Comment Written', 'required_count' => 1, 'type' => 'comment_written']);
        $user = User::factory()->create();  

        // Simulate a comment made by the new user.
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $user->refresh();

        // Simulate CommentWritten event dispatch
        event(new CommentWritten($comment));

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    /**
     * Test to ensure a user unlocks the '3 Comment Written' achievement 
     * after their 3 comments.
    */
    public function testUserUnlocksThreeCommentsWrittenAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();  

        // Dispatch the data and events.
        $this->createCommentsAndDispatchEvents($user, 3);

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written', '3 Comments Written'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    /**
     * Test to ensure a user unlocks the '5 Comment Written' achievement 
     * after their 5 comments.
    */
    public function testUserUnlocksFiveCommentsWrittenAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();

        // Dispatch the data and events.
        $this->createCommentsAndDispatchEvents($user, 5);

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written', '3 Comments Written', '5 Comments Written'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    /**
     * Test to ensure a user unlocks the '10 Comment Written' achievement 
     * after their 10 comments.
    */
    public function testUserUnlocksTenCommentsWrittenAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();

        // Dispatch the data and events.
        $this->createCommentsAndDispatchEvents($user, 10);

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written', '3 Comments Written', '5 Comments Written', '10 Comments Written'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    /**
     * Test to ensure a user unlocks the '20 Comment Written' achievement 
     * after their 20 comments.
    */
    public function testUserUnlocksTwentyCommentsWrittenAchievement()
    {
        // Generate necesary data for this test.
        $user = User::factory()->create();

        // Dispatch the data and events.
        $this->createCommentsAndDispatchEvents($user, 20);

        // Make a GET request to the achievements endpoint and assert the expected response
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Comment Written', '3 Comments Written', '5 Comments Written', '10 Comments Written', '20 Comments Written'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => '',
                'next_badge' => '',
                'remaining_to_unlock_next_badge' => 0
            ]);
    }

    /**
     * Test to ensure correct badge assignment based on the number of achievements unlocked.
     *
     * This test simulates unlocking a varying number of achievements for a user and then 
     * checks if the user is assigned the correct badge according to the predefined criteria.
     * It sequentially unlocks achievements and verifies the badge at each critical threshold,
     * covering all badge levels from 'Beginner' to 'Master'.
     */
    public function testUserBadgeAssignment()
    {
        $user = User::factory()->create();
        $this->createLessonsAndDispatchEvents($user, 10); 
        $this->createCommentsAndDispatchEvents($user, 3);

        $user->refresh();
        $this->assertEquals('Intermediate', $user->badge); 


        $this->createCommentsAndDispatchEvents($user, 7); 
        $this->createLessonsAndDispatchEvents($user, 50); 
        $user->refresh();
        $this->assertEquals('Advanced', $user->badge); 

        $this->createCommentsAndDispatchEvents($user, 10); 
        $this->createCommentsAndDispatchEvents($user, 7); 
        $user->refresh();
        $this->assertEquals('Master', $user->badge); 

    }

}