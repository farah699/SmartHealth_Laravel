@extends('partials.layouts.master')

@section('title', 'Ajouter Ressource | SmartHealth')
@section('title-sub', 'Admin')
@section('pagetitle', 'Ajouter une Ressource d’Aide')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4>Ajouter une Ressource</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('resources.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Sélectionner un type</option>
                            <option value="Urgence">Urgence</option>
                            <option value="Psychologue">Psychologue</option>
                            <option value="Centre Universitaire">Centre Universitaire</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="mb-3">
                        <label for="link" class="form-label">Lien (optionnel)</label>
                        <input type="url" class="form-control" id="link" name="link">
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection