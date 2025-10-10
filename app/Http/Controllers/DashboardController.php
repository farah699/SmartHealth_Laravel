<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Afficher la page dashboard principale après login
     */
    public function dashboard()
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Retourner le dashboard analytics par défaut
        return view('dashboard-analytics');
    }

    /**
     * Méthode générique pour toutes les vues (existante - modifiée)
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est connecté pour toutes les pages
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $path = $request->path();
        
        // Si c'est la racine, rediriger vers dashboard
        if ($path === '/') {
            return redirect()->route('dashboard');
        }

        // Vérifier si la vue existe
        if (view()->exists($path)) {
            return view($path);
        } else {
            abort(404);
        }
    }

    /**
     * Afficher différents types de dashboards
     */
    public function analytics()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('dashboard-analytics');
    }

    public function crm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('dashboard-crm');
    }

    public function ecommerce()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('dashboard-ecommerce');
    }

    public function project()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('dashboard-project');
    }
}