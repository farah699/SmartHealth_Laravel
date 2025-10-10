@extends('partials.layouts.master')

@section('title', 'Nutrition History | SmartHealth')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="page-title">📊 Nutrition History</h4>
                        <p class="text-muted mb-0">Track your nutritional evolution over time</p>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-calendar-check widget-icon bg-primary rounded-circle text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Days Tracked</h5>
                    <h3 class="mt-3 mb-1">{{ $stats['total_days'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2">
                            <i class="mdi mdi-arrow-up-bold"></i> Since beginning
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-target widget-icon bg-success rounded-circle text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Goals Achieved</h5>
                    <h3 class="mt-3 mb-1">{{ $stats['goals_achieved'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2">
                            <i class="mdi mdi-arrow-up-bold"></i> {{ $stats['success_rate'] }}%
                        </span>
                        Success rate
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-fire widget-icon bg-info rounded-circle text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Average Calories</h5>
                    <h3 class="mt-3 mb-1">{{ number_format($stats['avg_calories']) }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-info me-2">
                            <i class="mdi mdi-chart-line"></i> kcal/day
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-trophy widget-icon bg-warning rounded-circle text-white"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">Current Streak</h5>
                    <h3 class="mt-3 mb-1">{{ $stats['streak'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-warning me-2">
                            <i class="mdi mdi-fire"></i> Consecutive days
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($stats['best_day'])
    <!-- Best day -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success-lighten">
                                <span class="avatar-title rounded-circle bg-success">
                                    <i class="mdi mdi-star font-20 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0">🏆 Your best day!</h5>
                            <p class="mb-0">
                                <strong>{{ $stats['best_day']->summary_date->format('m/d/Y') }}</strong> - 
                                {{ number_format($stats['best_day']->total_calories) }} kcal 
                                ({{ number_format($stats['best_day']->calorie_percentage, 1) }}% of goal)
                                <span class="badge bg-success ms-2">{{ number_format($stats['best_day']->total_water_ml) }} ml water</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed history -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">📈 Detailed History</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="mdi mdi-filter-variant me-1"></i> Filters
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterHistory('all')">All days</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterHistory('success')">Goals achieved</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterHistory('failed')">Goals not achieved</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($history->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-centered mb-0" id="historyTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Calories</th>
                                        <th>Proteins</th>
                                        <th>Carbs</th>
                                        <th>Fats</th>
                                        <th>Hydration</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $day)
                                        <tr class="history-row" data-status="{{ $day->calorie_percentage >= 80 && $day->calorie_percentage <= 120 && $day->water_percentage >= 100 ? 'success' : 'failed' }}">
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $day->summary_date->format('d/m/Y') }}</strong>
                                                    <small class="text-muted">{{ $day->summary_date->format('l') }}</small>
                                                    <small class="text-muted">{{ $day->summary_date->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ number_format($day->total_calories, 0) }} kcal</strong>
                                                    <div class="progress mt-1" style="height: 6px;">
                                                        <div class="progress-bar 
                                                            @if($day->calorie_percentage < 70) bg-danger
                                                            @elseif($day->calorie_percentage < 90) bg-warning
                                                            @elseif($day->calorie_percentage <= 120) bg-success
                                                            @else bg-info
                                                            @endif" 
                                                            style="width: {{ min(100, $day->calorie_percentage) }}%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ number_format($day->calorie_percentage, 0) }}% / {{ number_format($day->calorie_goal, 0) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-lighten text-primary">
                                                    {{ number_format($day->total_proteins, 1) }}g
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning-lighten text-warning">
                                                    {{ number_format($day->total_carbs, 1) }}g
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-lighten text-info">
                                                    {{ number_format($day->total_fats, 1) }}g
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ number_format($day->total_water_ml, 0) }} ml</strong>
                                                    <div class="progress mt-1" style="height: 6px;">
                                                        <div class="progress-bar bg-info" style="width: {{ min(100, $day->water_percentage) }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ number_format($day->water_percentage, 0) }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $goalAchieved = $day->calorie_percentage >= 80 && $day->calorie_percentage <= 120 && $day->water_percentage >= 100;
                                                @endphp
                                                @if($goalAchieved)
                                                    <span class="badge bg-success fs-6">
                                                        <i class="mdi mdi-check-circle me-1"></i>
                                                        Perfect
                                                    </span>
                                                @elseif($day->calorie_percentage >= 70 && $day->water_percentage >= 70)
                                                    <span class="badge bg-warning fs-6">
                                                        <i class="mdi mdi-alert-circle me-1"></i>
                                                        Good
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="mdi mdi-close-circle me-1"></i>
                                                        Needs improvement
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    @if($goalAchieved)
                                                        <div class="text-success" style="font-size: 1.5rem;">
                                                            <i class="mdi mdi-trending-up"></i>
                                                        </div>
                                                    @elseif($day->calorie_percentage >= 70)
                                                        <div class="text-warning" style="font-size: 1.5rem;">
                                                            <i class="mdi mdi-trending-neutral"></i>
                                                        </div>
                                                    @else
                                                        <div class="text-danger" style="font-size: 1.5rem;">
                                                            <i class="mdi mdi-trending-down"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $history->links() }}
                        </div>
                    @else
                        <!-- Empty state -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="mdi mdi-history text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mt-3 text-muted">No history available</h5>
                            <p class="text-muted mb-4">
                                Start tracking your nutrition to see your history here.<br>
                                Your data will be automatically archived each day.
                            </p>
                            <div>
                                <a href="{{ route('nutrition.dashboard') }}" class="btn btn-primary me-2">
                                    <i class="mdi mdi-plus me-1"></i> Start tracking
                                </a>
                                <a href="{{ route('nutrition.profile') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-account-settings me-1"></i> Configure profile
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($history->count() > 0)
    <!-- Trends summary -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📊 Nutritional Trends</h5>
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ number_format($stats['avg_calories']) }}</h4>
                            <p class="text-muted mb-0">kcal/day</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info">{{ number_format($stats['avg_water']) }}</h4>
                            <p class="text-muted mb-0">ml/day</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">{{ $stats['success_rate'] }}%</h4>
                            <p class="text-muted mb-0">Success</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🎯 Personalized Tips</h5>
                    @if($stats['success_rate'] >= 80)
                        <div class="alert alert-success" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i>
                            <strong>Excellent work!</strong> You're maintaining your nutritional goals very well.
                        </div>
                    @elseif($stats['success_rate'] >= 60)
                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            <strong>Good work!</strong> A few adjustments could improve your results.
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>Keep up your efforts!</strong> Consistency is the key to success.
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('nutrition.profile') }}" class="btn btn-outline-primary btn-sm">
                            <i class="mdi mdi-account-edit me-1"></i> Adjust my goals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// History filtering function
function filterHistory(filter) {
    const rows = document.querySelectorAll('.history-row');
    
    rows.forEach(row => {
        const status = row.dataset.status;
        
        if (filter === 'all') {
            row.style.display = '';
        } else if (filter === 'success' && status === 'success') {
            row.style.display = '';
        } else if (filter === 'failed' && status === 'failed') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Statistics animation on load
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation (optional)
    const counters = document.querySelectorAll('.widget-flat h3');
    counters.forEach(counter => {
        counter.style.opacity = '0';
        counter.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            counter.style.transition = 'all 0.6s ease';
            counter.style.opacity = '1';
            counter.style.transform = 'translateY(0)';
        }, Math.random() * 500);
    });
});
</script>
@endsection