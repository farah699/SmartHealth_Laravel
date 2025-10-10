<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    // Affichage public de la liste des ressources
    public function index()
    {
        $resources = Resource::all();
        return view('resources.index', compact('resources'));
    }

    // Affichage du formulaire de création (admin only)
    public function create()
    {
        return view('resources.create');
    }

    // Stockage d'une nouvelle ressource
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
        ]);

        Resource::create($request->all());

        return redirect()->route('resources.admin')->with('success', 'Ressource ajoutée avec succès.');
    }

    // Affichage de la liste pour admin (avec options CRUD)
    public function admin()
    {
        $resources = Resource::all();
        return view('resources.admin', compact('resources'));
    }

    // Affichage du formulaire d'édition
    public function edit(Resource $resource)
    {
        return view('resources.edit', compact('resource'));
    }

    // Mise à jour d'une ressource
    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
        ]);

        $resource->update($request->all());

        return redirect()->route('resources.admin')->with('success', 'Ressource mise à jour avec succès.');
    }

    // Suppression d'une ressource
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('resources.admin')->with('success', 'Ressource supprimée avec succès.');
    }
}