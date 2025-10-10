<?php

namespace App\Services;

use App\Models\ExerciseVideo;
use App\Models\Exercise;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class VideoRecommendationService
{
    private $youtubeApiKey;
    
    public function __construct()
    {
        $this->youtubeApiKey = env('YOUTUBE_API_KEY');
    }

    /**
     * Get recommended videos for an exercise
     */
    public function getVideosForExercise($exerciseName, $exerciseType = null, $duration = null)
    {
        // First, check if we have videos in database
        $dbVideos = $this->getVideosFromDatabase($exerciseName, $exerciseType);
        
        if ($dbVideos->isNotEmpty()) {
            return $dbVideos;
        }

        // If no database videos, search YouTube
        if ($this->youtubeApiKey) {
            return $this->searchYouTubeVideos($exerciseName, $exerciseType, $duration);
        }

        // Fallback to curated video list
        return $this->getFallbackVideos($exerciseName, $exerciseType);
    }

    /**
     * Get videos from database
     */
    private function getVideosFromDatabase($exerciseName, $exerciseType)
    {
        return ExerciseVideo::where('exercise_name', 'LIKE', "%{$exerciseName}%")
            ->when($exerciseType, function($query) use ($exerciseType) {
                return $query->where('exercise_type', $exerciseType);
            })
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->take(3)
            ->get();
    }

    /**
     * Search YouTube videos
     */
    private function searchYouTubeVideos($exerciseName, $exerciseType, $duration)
    {
        $cacheKey = "youtube_videos_{$exerciseName}_{$exerciseType}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function() use ($exerciseName, $exerciseType, $duration) {
            try {
                // Build search query
                $searchQuery = $this->buildSearchQuery($exerciseName, $exerciseType, $duration);
                
                $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                    'key' => $this->youtubeApiKey,
                    'q' => $searchQuery,
                    'type' => 'video',
                    'part' => 'snippet',
                    'maxResults' => 5,
                    'order' => 'relevance',
                    'videoDuration' => $this->getYouTubeDuration($duration),
                    'videoDefinition' => 'high'
                ]);

                if ($response->successful()) {
                    $videos = $response->json()['items'] ?? [];
                    return $this->formatYouTubeVideos($videos);
                }

                return collect([]);

            } catch (\Exception $e) {
                \Log::error('YouTube API Error: ' . $e->getMessage());
                return collect([]);
            }
        });
    }

    /**
     * Build search query for YouTube
     */
    private function buildSearchQuery($exerciseName, $exerciseType, $duration)
    {
        $query = $exerciseName;
        
        // Add exercise type
        if ($exerciseType) {
            $typeTerms = [
                'cardio' => 'cardio workout',
                'strength' => 'strength training',
                'flexibility' => 'stretching yoga',
                'balance' => 'balance exercise'
            ];
            
            $query .= ' ' . ($typeTerms[$exerciseType] ?? $exerciseType);
        }

        // Add duration hint
        if ($duration) {
            if ($duration <= 15) {
                $query .= ' quick short';
            } elseif ($duration <= 30) {
                $query .= ' medium workout';
            } else {
                $query .= ' full workout';
            }
        }

        $query .= ' exercise tutorial';
        
        return $query;
    }

    /**
     * Get YouTube duration filter
     */
    private function getYouTubeDuration($duration)
    {
        if (!$duration) return 'any';
        
        if ($duration <= 4) return 'short'; // < 4 min
        if ($duration <= 20) return 'medium'; // 4-20 min
        return 'long'; // > 20 min
    }

    /**
     * Format YouTube videos response
     */
    private function formatYouTubeVideos($videos)
    {
        return collect($videos)->map(function($video) {
            return [
                'id' => $video['id']['videoId'],
                'title' => $video['snippet']['title'],
                'description' => $video['snippet']['description'],
                'thumbnail' => $video['snippet']['thumbnails']['medium']['url'] ?? '',
                'url' => "https://www.youtube.com/embed/{$video['id']['videoId']}",
                'watch_url' => "https://www.youtube.com/watch?v={$video['id']['videoId']}",
                'channel' => $video['snippet']['channelTitle'],
                'published_at' => $video['snippet']['publishedAt'],
                'source' => 'youtube'
            ];
        });
    }

    /**
     * Get fallback videos (curated list)
     */
    private function getFallbackVideos($exerciseName, $exerciseType)
    {
        $fallbackVideos = [
            'cardio' => [
                [
                    'title' => 'Cardio débutant - 20 minutes',
                    'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Replace with real URLs
                    'thumbnail' => '/images/cardio-thumbnail.jpg',
                    'description' => 'Séance de cardio adaptée aux débutants',
                    'duration' => '20:00'
                ]
            ],
            'strength' => [
                [
                    'title' => 'Renforcement musculaire sans matériel',
                    'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'thumbnail' => '/images/strength-thumbnail.jpg',
                    'description' => 'Exercices de renforcement au poids du corps',
                    'duration' => '15:00'
                ]
            ],
            'flexibility' => [
                [
                    'title' => 'Yoga doux pour débutants',
                    'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'thumbnail' => '/images/yoga-thumbnail.jpg',
                    'description' => 'Séance de yoga relaxante',
                    'duration' => '30:00'
                ]
            ]
        ];

        $videos = $fallbackVideos[$exerciseType] ?? $fallbackVideos['cardio'];
        
        return collect($videos);
    }

    /**
     * Save video recommendation feedback
     */
    public function recordVideoFeedback($videoId, $userId, $rating, $feedback = null)
    {
        // Implementation for saving user feedback on videos
        // This helps improve recommendations over time
    }
}