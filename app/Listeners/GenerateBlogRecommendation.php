<?php

namespace App\Listeners;

use App\Events\BlogCreated;
use App\Models\BlogRecommendation;
use App\Notifications\NewRecommendationNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateBlogRecommendation
{
    /**
     * Handle the event.
     */
    public function handle(BlogCreated $event): void
    {
        $blog = $event->blog;
        
        try {
            Log::info('🤖 Calling Blog AI API for recommendation', [
                'blog_id' => $blog->id,
                'blog_title' => $blog->title
            ]);

            // Appeler l'API Docker
            $response = Http::timeout(60)
                ->get(env('RECOMMENDATION_API_URL', 'http://blog-ai:5002') . '/api/recommend/' . $blog->id);

            Log::info('📥 API Response received', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    $mainRecommendation = $data['recommendation'] ?? null;
                    
                    if ($mainRecommendation) {
                        $recommendation = BlogRecommendation::updateOrCreate(
                            ['blog_id' => $blog->id],
                            [
                                'title' => $mainRecommendation['title'] ?? 'Ressource recommandée',
                                'description' => $mainRecommendation['description'] ?? '',
                                'category' => $mainRecommendation['category'] ?? '',
                                'content_type' => $mainRecommendation['content_type'] ?? '',
                                'difficulty_level' => $mainRecommendation['difficulty_level'] ?? 'N/A',
                                'estimated_time' => $mainRecommendation['estimated_time'] ?? 'N/A',
                                'target_audience' => $mainRecommendation['target_audience'] ?? 'Tous',
                                'url' => $mainRecommendation['url'] ?? '',  // ✅ IMPORTANT
                                'recommendations' => json_encode($data['recommendations'] ?? [$mainRecommendation]),
                                'similarity_scores' => json_encode($data['similarity_scores'] ?? []),
                                'ai_analysis' => json_encode($data['ai_analysis'] ?? []),
                                'generated_at' => now(),
                                'is_new' => true,
                                'email_sent' => false  // ✅ Initialiser à false
                            ]
                        );

                        Log::info('✅ Blog recommendation saved', [
                            'blog_id' => $blog->id,
                            'recommendation_id' => $recommendation->id,
                            'recommendation_title' => $recommendation->title,
                            'has_url' => !empty($recommendation->url)
                        ]);

                        // ✅ Envoyer l'email
                        try {
                            Log::info('📧 Attempting to send email', [
                                'user_id' => $blog->user->id,
                                'user_email' => $blog->user->email,
                                'user_name' => $blog->user->name
                            ]);

                            $blog->user->notify(new NewRecommendationNotification($blog, $recommendation));
                            
                            $recommendation->email_sent = true;
                            $recommendation->email_sent_at = now();
                            $recommendation->save();
                            
                            Log::info('✅ Email notification sent successfully', [
                                'user_email' => $blog->user->email,
                                'blog_id' => $blog->id,
                                'recommendation_id' => $recommendation->id
                            ]);
                            
                        } catch (\Exception $emailError) {
                            Log::error('❌ Email sending failed', [
                                'error' => $emailError->getMessage(),
                                'trace' => $emailError->getTraceAsString(),
                                'user_email' => $blog->user->email
                            ]);
                        }

                        // Session flash pour notification
                        session()->flash('new_recommendation', [
                            'blog_id' => $blog->id,
                            'recommendation_id' => $recommendation->id,
                            'title' => $recommendation->title,
                            'blog_title' => $blog->title
                        ]);
                        
                    } else {
                        Log::warning('⚠️ API returned success but no recommendation data', [
                            'blog_id' => $blog->id,
                            'response' => $data
                        ]);
                    }
                } else {
                    Log::warning('⚠️ API returned error', [
                        'blog_id' => $blog->id,
                        'message' => $data['message'] ?? 'Unknown error',
                        'response' => $data
                    ]);
                }
            } else {
                throw new \Exception('API returned status: ' . $response->status() . ' - Body: ' . $response->body());
            }
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('❌ Cannot connect to Blog AI service', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'url' => env('RECOMMENDATION_API_URL', 'http://blog-ai:5002')
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error generating recommendation', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}