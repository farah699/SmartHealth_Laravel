@extends('partials.layouts.master')

@section('title', 'My Nutrition Profile | SmartHealth')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">👤 My Nutrition Profile</h4>
               
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>Validation errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📋 Personal Information</h5>
                    
                    <form method="POST" action="{{ route('nutrition.profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Age -->
                            <div class="col-md-6 mb-3">
                                <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('age') is-invalid @enderror" 
                                       id="age" name="age" value="{{ old('age', $user->age) }}" 
                                       min="10" max="120" required>
                                @error('age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" 
                                        id="gender" name="gender" required>
                                    <option value="">Select...</option>
                                    <option value="male" {{ old('gender', $profile->gender) == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="female" {{ old('gender', $profile->gender) == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Height -->
                            <div class="col-md-6 mb-3">
                                <label for="height" class="form-label">Height (cm) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('height') is-invalid @enderror" 
                                       id="height" name="height" value="{{ old('height', $profile->height) }}" 
                                       min="100" max="250" step="0.1" required>
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Weight -->
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                       id="weight" name="weight" value="{{ old('weight', $profile->weight) }}" 
                                       min="30" max="300" step="0.1" required>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Activity level -->
                        <div class="mb-3">
                            <label for="activity_level" class="form-label">Activity level <span class="text-danger">*</span></label>
                            <select class="form-select @error('activity_level') is-invalid @enderror" 
                                    id="activity_level" name="activity_level" required>
                                <option value="">Select...</option>
                                <option value="sedentary" {{ old('activity_level', $profile->activity_level) == 'sedentary' ? 'selected' : '' }}>
                                    🛋️ Sedentary (office work, little exercise)
                                </option>
                                <option value="light" {{ old('activity_level', $profile->activity_level) == 'light' ? 'selected' : '' }}>
                                    🚶 Light activity (exercise 1-3 days/week)
                                </option>
                                <option value="moderate" {{ old('activity_level', $profile->activity_level) == 'moderate' ? 'selected' : '' }}>
                                    🏃 Moderate activity (exercise 3-5 days/week)
                                </option>
                                <option value="active" {{ old('activity_level', $profile->activity_level) == 'active' ? 'selected' : '' }}>
                                    💪 Very active (exercise 6-7 days/week)
                                </option>
                                <option value="very_active" {{ old('activity_level', $profile->activity_level) == 'very_active' ? 'selected' : '' }}>
                                    🏋️ Extremely active (intense exercise + physical work)
                                </option>
                            </select>
                            @error('activity_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Goal -->
                        <div class="mb-3">
                            <label for="goal" class="form-label">Goal <span class="text-danger">*</span></label>
                            <select class="form-select @error('goal') is-invalid @enderror" 
                                    id="goal" name="goal" required>
                                <option value="">Select...</option>
                                <option value="lose" {{ old('goal', $profile->goal) == 'lose' ? 'selected' : '' }}>
                                    📉 Lose weight (-500 kcal/day)
                                </option>
                                <option value="maintain" {{ old('goal', $profile->goal) == 'maintain' ? 'selected' : '' }}>
                                    ⚖️ Maintain my weight
                                </option>
                                <option value="gain" {{ old('goal', $profile->goal) == 'gain' ? 'selected' : '' }}>
                                    📈 Gain weight (+500 kcal/day)  
                                </option>
                            </select>
                            @error('goal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Goals preview -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🎯 Your Goals</h5>
                    
                    @if($profile->daily_calories)
                        <div class="mb-3">
                            <label class="form-label text-muted">Daily calories</label>
                            <h4 class="text-primary">{{ number_format($profile->daily_calories) }} kcal</h4>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Daily hydration</label>
                            <h4 class="text-info">{{ number_format($profile->daily_water_ml) }} ml</h4>
                        </div>

                        <hr>

                        <div class="small text-muted">
                            <div class="d-flex justify-content-between mb-1">
                                <span>BMR (basal metabolism)</span>
                                <strong>{{ $profile->bmr }} kcal</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>TDEE (total daily expenditure)</span>
                                <strong>{{ $profile->tdee }} kcal</strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="mdi mdi-calculator mdi-48px"></i>
                            <p class="mt-2">Fill out the form to calculate your personalized goals</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">💡 Tips</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-2">• Be honest about your activity level</li>
                        <li class="mb-2">• Weigh yourself fasting in the morning</li>
                        <li class="mb-2">• Goals are recalculated automatically</li>
                        <li>• Consult a professional for personalized follow-up</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection