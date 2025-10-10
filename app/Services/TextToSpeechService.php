<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TextToSpeechService
{
    /**
     * Générer l'audio pour un blog avec VoiceRSS
     */
    public function generateAudioForBlog(Blog $blog): ?string
    {
        try {
            $apiKey = env('VOICERSS_API_KEY');
            
            if (!$apiKey) {
                Log::error('❌ VoiceRSS API key not configured');
                return null;
            }

            // Détection de la langue
            $language = $this->detectLanguage($blog->content);
            $voiceMap = [
                'fr' => 'fr-fr',
                'en' => 'en-us',
                'ar' => 'ar-sa'
            ];
            $voiceLang = $voiceMap[$language] ?? 'en-us';

            // Préparer le texte (limité à 1000 caractères pour la version gratuite)
            $text = strip_tags($blog->content);
            $text = substr($text, 0, 1000);

            Log::info('🎵 Generating audio with VoiceRSS', [
                'blog_id' => $blog->id,
                'language' => $voiceLang,
                'text_length' => strlen($text)
            ]);

            // Appeler VoiceRSS API
            $response = Http::timeout(60)->asForm()->post('https://api.voicerss.org/', [
                'key' => $apiKey,
                'hl' => $voiceLang,
                'src' => $text,
                'c' => 'MP3',
                'f' => '44khz_16bit_stereo',
                'r' => '0'
            ]);

            if (!$response->successful()) {
                Log::error('❌ VoiceRSS API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $body = $response->body();
            
            // Vérifier si c'est une erreur de l'API
            if (str_contains($body, 'ERROR')) {
                Log::error('❌ VoiceRSS returned error', ['error' => $body]);
                return null;
            }

            // ✅ Format du nom de fichier cohérent
            $filename = 'audio/blog-' . $blog->id . '-' . $language . '-' . time() . '.mp3';
            
            // ✅ Créer le dossier audio s'il n'existe pas
            if (!Storage::disk('public')->exists('audio')) {
                Storage::disk('public')->makeDirectory('audio');
            }
            
            // Sauvegarder le fichier
            Storage::disk('public')->put($filename, $body);

            // ✅ Donner les permissions au fichier
            $fullPath = Storage::disk('public')->path($filename);
            @chmod($fullPath, 0644);

            // Vérifier que le fichier a été créé
            if (!Storage::disk('public')->exists($filename)) {
                Log::error('❌ Audio file was not created', ['filename' => $filename]);
                return null;
            }

            // Supprimer l'ancien audio si existant
            if ($blog->audio_url && $blog->audio_url !== $filename) {
                Storage::disk('public')->delete($blog->audio_url);
            }

            // ✅ CORRECTION : Calculer la durée estimée
            $duration = $this->estimateDuration($text);

            // Mettre à jour le blog
            $blog->update([
                'audio_url' => $filename,
                'audio_generated' => true,
                'estimated_duration' => $duration // ✅ Ajouter la durée
            ]);

            Log::info('✅ Audio generated successfully', [
                'blog_id' => $blog->id,
                'file' => $filename,
                'full_path' => $fullPath,
                'file_exists' => Storage::disk('public')->exists($filename),
                'file_size' => Storage::disk('public')->size($filename),
                'duration' => $duration,
                'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4)
            ]);

            return $filename;

        } catch (\Exception $e) {
            Log::error('❌ Error generating audio', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $blog->update(['audio_generated' => false]);
            return null;
        }
    }

    private function detectLanguage(string $text): string
    {
        $frenchWords = ['le', 'la', 'les', 'de', 'du', 'des', 'et', 'est', 'dans', 'pour'];
        $arabicPattern = '/[\x{0600}-\x{06FF}]/u';
        
        if (preg_match($arabicPattern, $text)) {
            return 'ar';
        }
        
        $frenchCount = 0;
        foreach ($frenchWords as $word) {
            $frenchCount += substr_count(strtolower($text), ' ' . $word . ' ');
        }
        
        return $frenchCount > 5 ? 'fr' : 'en';
    }

    /**
     * ✅ CORRECTION : Calculer la durée en secondes (pas en minutes)
     */
    private function estimateDuration(string $text): int
    {
        $wordCount = str_word_count(strip_tags($text));
        $wordsPerMinute = 150; // Vitesse moyenne de lecture
        $durationInMinutes = $wordCount / $wordsPerMinute;
        $durationInSeconds = (int) ceil($durationInMinutes * 60); // ✅ Convertir en secondes
        
        return max(1, $durationInSeconds); // Au moins 1 seconde
    }

    public function deleteAudio(?string $audioUrl): bool
    {
        if ($audioUrl && Storage::disk('public')->exists($audioUrl)) {
            return Storage::disk('public')->delete($audioUrl);
        }
        return false;
    }
}