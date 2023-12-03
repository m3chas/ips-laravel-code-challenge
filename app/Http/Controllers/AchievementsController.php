<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        // Determine current and next badge
        $currentBadge = $this->getCurrentBadge($user);
        $nextBadge = $this->getNextBadge($user);

        // Calculate remaining achievements to unlock next badge
        $remainingToUnlockNextBadge = $this->getRemainingToUnlockNextBadge($user, $nextBadge);

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
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
        // TODO: Seed achievements with rules
        return ['5 Lessons Watched'];
    }

    /**
     * Get the current badge of the user.
     *
     * @param  User  $user
     * @return string
     */
    private function getCurrentBadge(User $user)
    {
        // TODO: Calculate current user's badge
        return '';
    }

    /**
     * Get the next badge the user can earn.
     *
     * @param  User  $user
     * @return string
     */
    private function getNextBadge(User $user)
    {
        // TODO: Calculate current user's next badge
        return '';
    }

    /**
     * Calculate the number of achievements the user needs to unlock the next badge.
     *
     * @param  User  $user
     * @param  string  $nextBadge
     * @return int
     */
    private function getRemainingToUnlockNextBadge(User $user, $nextBadge)
    {
        // TODO: Implement the logic to calculate remaining achievements for next badge
        return 0;
    }
}
