<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResourceController; 
use App\Http\Controllers\YogaController;
use App\Http\Controllers\Auth\AuthController; // Correction: Ajouter Auth\
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NutritionController; // Ajoutez cette ligne pour NutritionController
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivitySessionController;
use App\Http\Controllers\AiFoodController;
use App\Http\Controllers\ImcController;
use App\Http\Controllers\AIRecommendationController; // Pour les recommandations IA
use App\Http\Controllers\TrainingController; // Pour la gestion des entraînements
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RecommendationController;

use App\Http\Controllers\WellnessCalendarController; // Ajout pour le calendrier bien-être
use Illuminate\Support\Facades\Auth;
// -----------------------
// Routes publiques
// -----------------------
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

//fusionner
// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/login-detailed', [LoginController::class, 'loginDetailed'])->name('login.detailed');

// Password reset
Route::get('/auth-reset-password', [ForgotPasswordController::class, 'showEmailForm'])->name('forgot.password.form');
Route::post('/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('forgot.password.sendOtp');
Route::get('/auth-two-step-verify', [ForgotPasswordController::class, 'showOtpForm'])->name('forgot.password.otpForm');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('forgot.password.verifyOtp');
Route::get('/auth-create-password', [ForgotPasswordController::class, 'showResetForm'])->name('forgot.password.resetForm');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('forgot.password.reset');

// Routes d'inscription avec OTP (publiques) - NOUVELLES ROUTES
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('register.verify.otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOtp'])->name('register.verify.otp.submit');
Route::post('/register/resend-otp', [AuthController::class, 'resendRegistrationOtp'])->name('register.resend.otp');

// Routes d'inscription avec OTP (publiques) - NOUVELLES ROUTES
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('register.verify.otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOtp'])->name('register.verify.otp.submit');
Route::post('/register/resend-otp', [AuthController::class, 'resendRegistrationOtp'])->name('register.resend.otp');

// Route publique pour les ressources
Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');

// -----------------------
// Routes protégées (auth)
// -----------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/yoga', [YogaController::class, 'practice'])->name('yoga.practice');
    Route::post('/yoga/start-session', [YogaController::class, 'startSession'])->name('yoga.start');
    Route::post('/yoga/detect-pose', [YogaController::class, 'detectPose'])->name('yoga.detect');
    Route::post('/yoga/end-session', [YogaController::class, 'endSession'])->name('yoga.end');
    Route::get('/yoga/stats', [YogaController::class, 'getStats'])->name('yoga.stats');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/yoga', [YogaController::class, 'practice'])->name('yoga.practice');
    Route::post('/yoga/start-session', [YogaController::class, 'startSession'])->name('yoga.start');
    Route::post('/yoga/detect-pose', [YogaController::class, 'detectPose'])->name('yoga.detect');
    Route::post('/yoga/end-session', [YogaController::class, 'endSession'])->name('yoga.end');
    Route::get('/yoga/stats', [YogaController::class, 'getStats'])->name('yoga.stats');
});

Route::middleware('auth')->group(function () {

    // *** IMPORTANT : Routes spécifiques AVANT la route catch-all ***
    
// Routes pour les notifications (CORRIGÉES)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    
    // CORRECTION : Supporter GET et POST pour markAsRead
    Route::match(['GET', 'POST'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    
    // Routes pour les blogs (MAINTENANT protégées correctement)
    Route::get('/pages-blog-list', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/pages-blog-create', [BlogController::class, 'create'])->name('blogs.create');
    Route::post('/pages-blog-create', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/pages-blog-details/{id}', [BlogController::class, 'show'])->name('blogs.show');
    Route::get('/pages-blog-edit/{id}', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/pages-blog-edit/{id}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/pages-blog-delete/{id}', [BlogController::class, 'destroy'])->name('blogs.destroy');

    // Routes pour les favoris
    Route::post('/blogs/{blog}/favorite', [FavoriteController::class, 'toggle'])->name('blogs.favorite');
    Route::post('/blogs/{blog}/mark-read', [FavoriteController::class, 'markAsRead'])->name('blogs.mark-read');
    Route::get('/my-favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::post('/blogs/{id}/regenerate-audio', [BlogController::class, 'regenerateAudio'])->name('blogs.regenerate-audio');
     // Routes pour les commentaires
    Route::post('/blogs/{blog}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike'])->name('comments.like');

    // Recommendation routes
Route::middleware('auth')->prefix('recommendations')->name('recommendations.')->group(function () {
    Route::get('/', [RecommendationController::class, 'index'])->name('index');
    Route::get('/my', [RecommendationController::class, 'myRecommendations'])->name('my');
    Route::get('/{id}', [RecommendationController::class, 'show'])->name('show');
    Route::delete('/{id}', [RecommendationController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/mark-as-read', [RecommendationController::class, 'markAsRead'])->name('markAsRead');

    // 🆕 Nouvelles routes API Blog AI
    Route::post('/recommendations/regenerate/{blogId}', [RecommendationController::class, 'regenerate'])->name('recommendations.regenerate');
    Route::get('/blog-ai/test', [RecommendationController::class, 'testConnection'])->name('blog-ai.test');
    Route::get('/blog-ai/stats', [RecommendationController::class, 'stats'])->name('blog-ai.stats');
});

    
    // Questionnaires
    Route::prefix('questionnaires')->group(function () {
        Route::get('/', [QuestionnaireController::class, 'index'])->name('questionnaires.index');
        Route::get('/start', [QuestionnaireController::class, 'start'])->name('questionnaires.start');
        Route::get('/{type}', [QuestionnaireController::class, 'show'])->name('questionnaires.show');
        Route::post('/{type}', [QuestionnaireController::class, 'store'])->name('questionnaires.store');
        Route::get('/result/session', [QuestionnaireController::class, 'result'])->name('questionnaires.result');
        Route::get('/history/all', [QuestionnaireController::class, 'history'])->name('questionnaires.history');
    });

    // *** NOUVELLES ROUTES POUR LE PROFIL ***
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar'); // NOUVELLE ROUTE
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

    // Routes Nutrition
    Route::prefix('nutrition')->name('nutrition.')->middleware('auth')->group(function () {
        // Pages principales
        Route::get('/', [NutritionController::class, 'dashboard'])->name('dashboard');
        Route::get('/history', [NutritionController::class, 'history'])->name('history');
        Route::get('/profile', [NutritionController::class, 'profile'])->name('profile');
        Route::post('/profile', [NutritionController::class, 'updateProfile'])->name('profile.update');
        
        // Routes Hydratation
        Route::post('/hydration', [NutritionController::class, 'storeHydration'])->name('hydration.store');
        Route::post('/hydration/quick', [NutritionController::class, 'quickAddWater'])->name('hydration.quick');
        Route::delete('/hydration/{id}', [NutritionController::class, 'deleteHydration'])->name('hydration.delete');
        Route::get('/hydration/stats', [NutritionController::class, 'getHydrationStats'])->name('hydration.stats');
        
        // Routes Aliments
        Route::get('/food/search', [NutritionController::class, 'searchFood'])->name('food.search');
        Route::post('/food/calculate-nutrition', [NutritionController::class, 'calculateNutrition'])->name('food.calculate-nutrition');
        Route::post('/food/store', [NutritionController::class, 'storeFood'])->name('food.store');
        Route::delete('/food/{id}', [NutritionController::class, 'deleteFood'])->name('food.delete');
    });

    // *** ROUTES WELLNESS CORRIGÉES ET COMPLÈTES ***
    Route::prefix('wellness')->name('wellness.')->group(function () {
        // Pages principales
        Route::get('/', [WellnessCalendarController::class, 'index'])->name('calendar');
        Route::get('/calendar', [WellnessCalendarController::class, 'index'])->name('calendar.alt');
        Route::get('/dashboard', [WellnessCalendarController::class, 'index'])->name('dashboard');
        
        // API pour les événements wellness
        Route::get('/events', [WellnessCalendarController::class, 'getEvents'])->name('events.index');
        Route::post('/events', [WellnessCalendarController::class, 'store'])->name('events.store');
        Route::get('/events/{id}', [WellnessCalendarController::class, 'show'])->name('events.show');
        Route::put('/events/{id}', [WellnessCalendarController::class, 'update'])->name('events.update');
        Route::delete('/events/{id}', [WellnessCalendarController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{id}/complete', [WellnessCalendarController::class, 'complete'])->name('events.complete');
        
        // API pour les statistiques et recommandations
        Route::get('/stats/today', [WellnessCalendarController::class, 'getTodayStats'])->name('stats.today');
       Route::get('/stats/weekly', [WellnessCalendarController::class, 'getWeeklyStats'])->name('stats.weekly');
Route::get('/ai/recommendations', [WellnessCalendarController::class, 'getAIRecommendations'])->name('ai.recommendations');
// Garde une alias plate si tu veux
Route::get('/recommendations', [WellnessCalendarController::class, 'getAIRecommendations'])->name('recommendations');
        
        // Routes supplémentaires pour les catégories
        Route::get('/categories', [WellnessCalendarController::class, 'getCategories'])->name('categories.index');
        Route::post('/categories', [WellnessCalendarController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [WellnessCalendarController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [WellnessCalendarController::class, 'destroyCategory'])->name('categories.destroy');
        
        // Routes pour l'export et l'import
        Route::get('/export', [WellnessCalendarController::class, 'exportData'])->name('export');
        Route::post('/import', [WellnessCalendarController::class, 'importData'])->name('import');
        
        // Routes pour les rapports
        Route::get('/reports/monthly', [WellnessCalendarController::class, 'monthlyReport'])->name('reports.monthly');
        Route::get('/reports/weekly', [WellnessCalendarController::class, 'weeklyReport'])->name('reports.weekly');
        Route::get('/reports/stress-analysis', [WellnessCalendarController::class, 'stressAnalysis'])->name('reports.stress');
    });

    // *** REDIRECTION POUR apps-calendar ***
    Route::get('/apps-calendar', function() {
        return redirect()->route('wellness.calendar');
    })->name('apps.calendar');
  
     // *** NOUVELLES ROUTES POUR LE PROFIL ***
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar'); // NOUVELLE ROUTE
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

    
   Route::prefix('nutrition')->name('nutrition.')->middleware('auth')->group(function () {
    // Pages principales
    Route::get('/', [NutritionController::class, 'dashboard'])->name('dashboard');
    Route::get('/history', [NutritionController::class, 'history'])->name('history');
    Route::get('/profile', [NutritionController::class, 'profile'])->name('profile');
    Route::post('/profile', [NutritionController::class, 'updateProfile'])->name('profile.update');
    
    // Routes Hydratation
    Route::post('/hydration', [NutritionController::class, 'storeHydration'])->name('hydration.store');
    Route::post('/hydration/quick', [NutritionController::class, 'quickAddWater'])->name('hydration.quick');
    Route::delete('/hydration/{id}', [NutritionController::class, 'deleteHydration'])->name('hydration.delete');
    Route::get('/hydration/stats', [NutritionController::class, 'getHydrationStats'])->name('hydration.stats');
    
    // Routes Aliments
    Route::get('/food/search', [NutritionController::class, 'searchFood'])->name('food.search');
    Route::post('/food/calculate-nutrition', [NutritionController::class, 'calculateNutrition'])->name('food.calculate-nutrition');
    Route::post('/food/store', [NutritionController::class, 'storeFood'])->name('food.store');
    Route::delete('/food/{id}', [NutritionController::class, 'deleteFood'])->name('food.delete');
    
    // ✅ NOUVELLES ROUTES IA
    Route::post('/ai/analyze-food', [AiFoodController::class, 'analyzeFood'])->name('ai.analyze-food');
    Route::post('/ai/save-foods', [AiFoodController::class, 'saveFoodsFromAi'])->name('ai.save-foods');
});

  

     // *** NOUVELLES ROUTES POUR LE PROFIL ***
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar'); // NOUVELLE ROUTE
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

    
   Route::prefix('nutrition')->name('nutrition.')->middleware('auth')->group(function () {
    // Pages principales
    Route::get('/', [NutritionController::class, 'dashboard'])->name('dashboard');
    Route::get('/history', [NutritionController::class, 'history'])->name('history');
    Route::get('/profile', [NutritionController::class, 'profile'])->name('profile');
    Route::post('/profile', [NutritionController::class, 'updateProfile'])->name('profile.update');
    
    // Routes Hydratation
    Route::post('/hydration', [NutritionController::class, 'storeHydration'])->name('hydration.store');
    Route::post('/hydration/quick', [NutritionController::class, 'quickAddWater'])->name('hydration.quick');
    Route::delete('/hydration/{id}', [NutritionController::class, 'deleteHydration'])->name('hydration.delete');
    Route::get('/hydration/stats', [NutritionController::class, 'getHydrationStats'])->name('hydration.stats');
    
    // Routes Aliments
    Route::get('/food/search', [NutritionController::class, 'searchFood'])->name('food.search');
    Route::post('/food/calculate-nutrition', [NutritionController::class, 'calculateNutrition'])->name('food.calculate-nutrition');
    Route::post('/food/store', [NutritionController::class, 'storeFood'])->name('food.store');
    Route::delete('/food/{id}', [NutritionController::class, 'deleteFood'])->name('food.delete');
    
    
});

  
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Dashboards spécifiques
    Route::get('/dashboard-analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard-crm', [DashboardController::class, 'crm'])->name('dashboard.crm');
    Route::get('/dashboard-ecommerce', [DashboardController::class, 'ecommerce'])->name('dashboard.ecommerce');
    Route::get('/dashboard-project', [DashboardController::class, 'project'])->name('dashboard.project');

    // *** NOUVELLES ROUTES POUR L'ADMINISTRATION ***
    Route::prefix('admin')->middleware('auth')->group(function () { // You can add a role middleware here later
        Route::get('user-stats', [AdminController::class, 'userStats'])->name('admin.user-stats');
        Route::delete('users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
        Route::put('users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    });

    // Routes admin pour les ressources
    Route::prefix('admin/resources')->group(function () {
        Route::get('/', [ResourceController::class, 'admin'])->name('resources.admin');
        Route::get('/create', [ResourceController::class, 'create'])->name('resources.create');
        Route::post('/', [ResourceController::class, 'store'])->name('resources.store');
        Route::get('/{resource}/edit', [ResourceController::class, 'edit'])->name('resources.edit');
        Route::put('/{resource}', [ResourceController::class, 'update'])->name('resources.update');
        Route::delete('/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');
    });

    // Routes pour les activités sportives
Route::middleware('auth')->group(function () {
            Route::get('/activities/statistics', [ActivityController::class, 'statistics'])->name('activities.statistics');

    Route::resource('activities', ActivityController::class);
    
    // Routes spéciales pour les activités
    Route::get('/activities/stats/dashboard', [ActivityController::class, 'statsDashboard'])->name('activities.stats');
    Route::post('/activities/{activity}/duplicate', [ActivityController::class, 'duplicate'])->name('activities.duplicate');
});


    // Route pour convertir en récurrente
    Route::put('activities/{activity}/convert-recurring', [ActivityController::class, 'convertRecurring'])
        ->name('activities.convert-recurring');
    
    // Routes pour les sessions d'activités
    Route::get('activities/{activity}/sessions/create', [ActivitySessionController::class, 'create'])
        ->name('activity-sessions.create');
    Route::post('activities/{activity}/sessions', [ActivitySessionController::class, 'store'])
        ->name('activity-sessions.store');
    Route::get('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'show'])
        ->name('activity-sessions.show');
    Route::get('activities/{activity}/sessions/{session}/edit', [ActivitySessionController::class, 'edit'])
        ->name('activity-sessions.edit');
    Route::put('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'update'])
        ->name('activity-sessions.update');
    Route::delete('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'destroy'])
        ->name('activity-sessions.destroy');



    // Routes pour les activités sportives
Route::middleware('auth')->group(function () {
            Route::get('/activities/statistics', [ActivityController::class, 'statistics'])->name('activities.statistics');

    Route::resource('activities', ActivityController::class);
    
    // Routes spéciales pour les activités
    Route::get('/activities/stats/dashboard', [ActivityController::class, 'statsDashboard'])->name('activities.stats');
    Route::post('/activities/{activity}/duplicate', [ActivityController::class, 'duplicate'])->name('activities.duplicate');
});


    // Route pour convertir en récurrente
    Route::put('activities/{activity}/convert-recurring', [ActivityController::class, 'convertRecurring'])
        ->name('activities.convert-recurring');
    
    // Routes pour les sessions d'activités
    Route::get('activities/{activity}/sessions/create', [ActivitySessionController::class, 'create'])
        ->name('activity-sessions.create');
    Route::post('activities/{activity}/sessions', [ActivitySessionController::class, 'store'])
        ->name('activity-sessions.store');
    Route::get('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'show'])
        ->name('activity-sessions.show');
    Route::get('activities/{activity}/sessions/{session}/edit', [ActivitySessionController::class, 'edit'])
        ->name('activity-sessions.edit');
    Route::put('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'update'])
        ->name('activity-sessions.update');
    Route::delete('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'destroy'])
        ->name('activity-sessions.destroy');

// Routes IMC
Route::get('/imc', [ImcController::class, 'index'])->name('imc.index');

// API Routes
Route::prefix('api')->group(function () {
    Route::post('/imc/calculate', [ImcController::class, 'calculate']);
    Route::post('/imc/save-profile', [ImcController::class, 'saveToProfile'])->middleware('auth');
    Route::get('/imc/user/{userId}', [ImcController::class, 'getUserImc']);
    Route::get('/imc/all-users', [ImcController::class, 'getAllUsersImc']);
});



// Routes pour les recommandations IA
Route::middleware(['auth'])->group(function () {
// Routes pour les recommandations IA
    Route::get('/exercise/recommendations', [AIRecommendationController::class, 'index'])
        ->name('exercises.recommendations');
    
    Route::post('/exercise/recommendations/generate', [AIRecommendationController::class, 'generateRecommendations'])
        ->name('exercises.recommendations.generate');

         Route::post('/training/start', [TrainingController::class, 'startTraining'])
        ->name('training.start');
    
    Route::post('/training/complete', [TrainingController::class, 'completeTraining'])
        ->name('training.complete');

});
    



    // Routes pour les activités sportives
    Route::middleware('auth')->group(function () {
        Route::get('/activities/statistics', [ActivityController::class, 'statistics'])->name('activities.statistics');

        Route::resource('activities', ActivityController::class);
        
        // Routes spéciales pour les activités
        Route::get('/activities/stats/dashboard', [ActivityController::class, 'statsDashboard'])->name('activities.stats');
        Route::post('/activities/{activity}/duplicate', [ActivityController::class, 'duplicate'])->name('activities.duplicate');
    });

    // Route pour convertir en récurrente
    Route::put('activities/{activity}/convert-recurring', [ActivityController::class, 'convertRecurring'])
        ->name('activities.convert-recurring');
    
    // Routes pour les sessions d'activités
    Route::get('activities/{activity}/sessions/create', [ActivitySessionController::class, 'create'])
        ->name('activity-sessions.create');
    Route::post('activities/{activity}/sessions', [ActivitySessionController::class, 'store'])
        ->name('activity-sessions.store');
    Route::get('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'show'])
        ->name('activity-sessions.show');
    Route::get('activities/{activity}/sessions/{session}/edit', [ActivitySessionController::class, 'edit'])
        ->name('activity-sessions.edit');
    Route::put('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'update'])
        ->name('activity-sessions.update');
    Route::delete('activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'destroy'])
        ->name('activity-sessions.destroy');

    // Catch-all uniquement pour les pages du dashboard (pas pour questionnaires ou resources)
    //Route::get('dashboard/{any}', [DashboardController::class, 'index'])
        //->where('any', '.*')
        //->name('dashboard.catchall');

         // *** SOLUTION : Route catch-all élargie pour TOUTES les pages du template ***
    Route::get('{any}', [DashboardController::class, 'index'])
        ->where('any', '^(?!api|storage|questionnaires|pages-blog|admin|notifications|profile).*') // Exclut les préfixes spéciaux
        ->name('template.catchall');
});