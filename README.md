# Back-end Developer Challenge Solution

## Overview

This document outlines the solution to the Back-end Developer Test, focusing on implementing a system for tracking user achievements and badges within a course portal.

## Key Features

### Achievements System
- Implemented logic to track user activities like watching lessons and writing comments.
- Created milestones such as "First Lesson Watched" and "5 Lessons Watched".

### Badges System
- Developed a badge system that awards titles like "Beginner", "Intermediate", based on the number of achievements unlocked.

### Event-Driven Architecture
- Introduced events like `AchievementUnlocked` and `BadgeUnlocked`, fired upon unlocking each achievement or badge.

### RESTful Endpoint
- Created an endpoint `users/{user}/achievements` to display a user’s achievements and badges.

### Test Coverage
- Ensured comprehensive test coverage for all functionalities.

## Technical Implementation

- **Eloquent Relationships**: Leveraged for `watched` and `comments` in the User model.
- **Event Handling**: Handled `LessonWatched` and `CommentWritten` events for updating achievements and badges.
- **Scalable Achievement Logic**: Designed for easy integration of new achievements or badges.
- **Database Setup**: Utilized MySQL with migrations for database structure.

## Testing Strategy

- **Comprehensive Coverage**: Included tests for all possible scenarios.
- **HTTP Testing**: Used Laravel's HTTP testing for simulating user actions.

## Setup and Installation

- **Repository Setup**: Provided GitHub repository with complete commit history.
- **Local Database Configuration**: Instructions for setting up MySQL and .env configuration.
- **Migration Execution**: Steps to run migrations for database setup.

## Submission Standards

- **Criteria Fulfillment**: Met all specified requirements.
- **Code Quality**: Emphasized readability and scalability.
- **Robust Testing**: Focused on ensuring reliability for production deployment.

## Conclusion

This solution showcases a thorough approach to the Back-end Developer Test, reflecting proficiency in Laravel, RESTful API development, and event-driven design.

---

