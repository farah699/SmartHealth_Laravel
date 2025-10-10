<?php


namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['course', 'marche', 'velo', 'fitness'];
        $intensities = ['faible', 'modere', 'intense'];
        $statuses = ['active', 'completed'];
        $weathers = ['ensoleille', 'nuageux', 'pluvieux', 'venteux', 'froid'];
        
        $type = $this->faker->randomElement($types);
        $intensity = $this->faker->randomElement($intensities);
        $isRecurring = $this->faker->boolean(30); // 30% de chance d'être récurrent
        
        // Noms d'activités selon le type
        $activityNames = [
            'course' => ['Footing matinal', 'Course du soir', 'Jogging au parc', 'Course longue', 'Sprint training'],
            'marche' => ['Promenade santé', 'Marche rapide', 'Balade en forêt', 'Marche urbaine', 'Randonnée'],
            'velo' => ['Sortie vélo', 'Cyclisme route', 'VTT', 'Vélo urbain', 'Entraînement vélo'],
            'fitness' => ['Séance fitness', 'Musculation', 'Cardio training', 'HIIT', 'Renforcement']
        ];
        
        $name = $this->faker->randomElement($activityNames[$type]);
        
        // Durée selon le type d'activité
        $durationRanges = [
            'course' => [20, 90],
            'marche' => [30, 120],
            'velo' => [30, 180],
            'fitness' => [30, 90]
        ];
        
        $duration = $this->faker->numberBetween(...$durationRanges[$type]);
        
        // Distance selon le type (peut être null)
        $distance = null;
        if (in_array($type, ['course', 'marche', 'velo'])) {
            $distanceRanges = [
                'course' => [2, 15],
                'marche' => [1, 10],
                'velo' => [5, 50]
            ];
            $distance = $this->faker->randomFloat(2, ...$distanceRanges[$type]);
        }
        
        // Calcul des calories selon le type, durée et intensité
        $caloriesPerMinute = [
            'course' => ['faible' => 8, 'modere' => 12, 'intense' => 16],
            'marche' => ['faible' => 4, 'modere' => 6, 'intense' => 8],
            'velo' => ['faible' => 6, 'modere' => 10, 'intense' => 14],
            'fitness' => ['faible' => 5, 'modere' => 8, 'intense' => 12]
        ];
        
        $calories = $duration * $caloriesPerMinute[$type][$intensity];
        $calories = $calories + $this->faker->numberBetween(-50, 50); // Variation réaliste
        
        // Données supplémentaires
        $additionalData = [];
        if ($this->faker->boolean(60)) { // 60% de chance d'avoir des données supplémentaires
            $additionalData['heart_rate'] = $this->faker->numberBetween(100, 180);
        }
        if ($this->faker->boolean(40)) { // 40% de chance d'avoir la météo
            $additionalData['weather'] = $this->faker->randomElement($weathers);
        }
        
        return [
            'user_id' => User::factory(), // Créera un utilisateur ou utilisera un existant
            'name' => $name,
            'type' => $type,
            'duration' => $isRecurring ? null : $duration, // Null si récurrent
            'distance' => $isRecurring ? null : $distance, // Null si récurrent
            'calories' => $isRecurring ? null : $calories, // Null si récurrent
            'description' => $this->faker->boolean(70) ? $this->faker->sentence(10) : null,
            'activity_date' => $isRecurring ? $this->faker->dateTimeBetween('-1 month', 'now') : $this->faker->dateTimeBetween('-2 months', 'now'),
            'start_time' => $this->faker->time('H:i'),
            'intensity' => $intensity,
            'additional_data' => empty($additionalData) ? null : $additionalData,
            'is_recurring' => $isRecurring,
        'status' => $this->faker->randomElement($statuses),
            'target_sessions_per_week' => $isRecurring ? $this->faker->numberBetween(2, 6) : null,
            'activity_description' => $isRecurring ? $this->faker->sentence(6) : null,
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            }
        ];
    }

    /**
     * Indicate that the activity is recurring.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'duration' => null,
            'distance' => null,
            'calories' => null,
            'target_sessions_per_week' => $this->faker->numberBetween(2, 6),
            'activity_description' => $this->faker->sentence(6),
        ]);
    }

    /**
     * Indicate that the activity is a single session.
     */
    public function singleSession(): static
    {
        return $this->state(function (array $attributes) {
            $type = $attributes['type'] ?? 'course';
            $intensity = $attributes['intensity'] ?? 'modere';
            
            $durationRanges = [
                'course' => [20, 90],
                'marche' => [30, 120],
                'velo' => [30, 180],
                'fitness' => [30, 90]
            ];
            
            $duration = $this->faker->numberBetween(...$durationRanges[$type]);
            
            $distance = null;
            if (in_array($type, ['course', 'marche', 'velo'])) {
                $distanceRanges = [
                    'course' => [2, 15],
                    'marche' => [1, 10],
                    'velo' => [5, 50]
                ];
                $distance = $this->faker->randomFloat(2, ...$distanceRanges[$type]);
            }
            
            $caloriesPerMinute = [
                'course' => ['faible' => 8, 'modere' => 12, 'intense' => 16],
                'marche' => ['faible' => 4, 'modere' => 6, 'intense' => 8],
                'velo' => ['faible' => 6, 'modere' => 10, 'intense' => 14],
                'fitness' => ['faible' => 5, 'modere' => 8, 'intense' => 12]
            ];
            
            $calories = $duration * $caloriesPerMinute[$type][$intensity];
            
            return [
                'is_recurring' => false,
                'duration' => $duration,
                'distance' => $distance,
                'calories' => $calories,
                'target_sessions_per_week' => null,
                'activity_description' => null,
            ];
        });
    }

    /**
     * Indicate that the activity is of a specific type.
     */
    public function ofType(string $type): static
    {
        $activityNames = [
            'course' => ['Footing matinal', 'Course du soir', 'Jogging au parc', 'Course longue', 'Sprint training'],
            'marche' => ['Promenade santé', 'Marche rapide', 'Balade en forêt', 'Marche urbaine', 'Randonnée'],
            'velo' => ['Sortie vélo', 'Cyclisme route', 'VTT', 'Vélo urbain', 'Entraînement vélo'],
            'fitness' => ['Séance fitness', 'Musculation', 'Cardio training', 'HIIT', 'Renforcement']
        ];

        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'name' => $this->faker->randomElement($activityNames[$type] ?? ['Activité sportive']),
        ]);
    }

    /**
     * Indicate that the activity is for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}