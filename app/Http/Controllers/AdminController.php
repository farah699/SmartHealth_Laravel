<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     * We can add a middleware to ensure only admins (e.g., 'Teacher' role) can access.
     * For this, you would typically create a role middleware.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Example of a role middleware check
        // $this->middleware('role:Teacher');
    }

    /**
     * Show the admin dashboard with user statistics.
     *
     * @return \Illuminate\View\View
     */
    public function userStats()
    {
        // Basic Stats
        $totalUsers = User::count();
        $studentCount = User::where('role', 'Student')->count();
        $teacherCount = User::where('role', 'Teacher')->count();
        $enabledUsers = User::where('enabled', true)->count();

        // Data for Registration Chart (last 30 days)
        $registrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format data for the chart
        $registrationLabels = $registrations->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $registrationData = $registrations->pluck('count');

        // All users for the table
        $users = User::latest()->get();

        return view('admin.user-stats', compact(
            'totalUsers', 'studentCount', 'teacherCount', 'enabledUsers',
            'registrationLabels', 'registrationData', 'users'
        ));
    }

    /**
     * Supprimer un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Empêcher un admin de se supprimer lui-même
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous не pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return back()->with('success', "L'utilisateur {$user->name} a été supprimé avec succès.");
    }

    /**
     * Activer ou désactiver un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->enabled = !$user->enabled;
        $user->save();

        return back()->with('success', "Le statut de {$user->name} a été mis à jour.");
    }
}