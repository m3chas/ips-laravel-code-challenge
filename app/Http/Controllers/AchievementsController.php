<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    /**
     * Display a listing of the user's achievements and badges.
     *
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user)
    {
        // Retrieve user's unlocked achievements
        $unlockedAchievements = $user->achievements->pluck('name');

        // Determine the next available achievements
        $nextAvailableAchievements = $this->getNextAvailableAchievements($user);

        // Determine next badge
        $nextBadge = $this->getNextBadge($user);

        // Calculate remaining achievements to unlock next badge
        $remainingToUnlockNextBadge = $this->getRemainingToUnlockNextBadge($user, $nextBadge);

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $user->badge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge
        ]);
    }

    /**
     * Get the next available achievements for the user.
     *
     * @param  User  $user
     * @return array
     */
    private function getNextAvailableAchievements(User $user)
    {
        // Initialize counts to 0 if no achievements are unlocked
        $highestLessonAchievementCount = $user->achievements()
            ->where('type', 'lesson_watched')
            ->max('required_count') ?? 0;

        $highestCommentAchievementCount = $user->achievements()
            ->where('type', 'comment_written')
            ->max('required_count') ?? 0;

        // Get the next lesson and comment achievements
        $nextLessonAchievement = Achievement::where('type', 'lesson_watched')
                                            ->where('required_count', '>', $highestLessonAchievementCount)
                                            ->orderBy('required_count', 'asc')
                                            ->first();

        $nextCommentAchievement = Achievement::where('type', 'comment_written')
                                            ->where('required_count', '>', $highestCommentAchievementCount)
                                            ->orderBy('required_count', 'asc')
                                            ->first();

        // Prepare the list of next available achievements
        $nextAvailableAchievements = [];
        if ($nextLessonAchievement) {
            $nextAvailableAchievements[] = $nextLessonAchievement->name;
        }
        if ($nextCommentAchievement) {
            $nextAvailableAchievements[] = $nextCommentAchievement->name;
        }

        return $nextAvailableAchievements;
    }

    /**
     * Get the next badge the user can earn.
     *
     * @param  User  $user
     * @return string
     */
    private function getNextBadge(User $user)
    {
        $currentBadgeName = $user->badge;
        $currentBadge = Badge::where('name', $currentBadgeName)->first();

        // Handle the case where the current badge is the highest available
        if (!$currentBadge) {
            return null;
        }

        $nextBadge = Badge::where('achievement_count', '>', $currentBadge->achievement_count)
                        ->orderBy('achievement_count', 'asc')
                        ->first();

        return $nextBadge ? $nextBadge->name : null;
    }

    /**
     * Calculate the number of achievements the user needs to unlock the next badge.
     *
     * @param  User  $user
     * @param  string  $nextBadge
     * @return int
     */
    private function getRemainingToUnlockNextBadge(User $user, $nextBadgeName)
    {
        if (!$nextBadgeName) {
            return 0;
        }

        $nextBadge = Badge::where('name', $nextBadgeName)->first();
        // Add a null check for nextBadge
        if (!$nextBadge) {
            return 0;
        }
    
        $userAchievementCount = $user->achievements->count();
        return max(0, $nextBadge->achievement_count - $userAchievementCount);
    }
}
