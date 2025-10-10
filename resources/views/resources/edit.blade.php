@extends('partials.layouts.master')

@section('title', 'Éditer Ressource | SmartHealth')
@section('title-sub', 'Admin')
@section('pagetitle', 'Éditer une Ressource d’Aide')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4>Éditer la Ressource</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('resources.update', $resource) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <input type="text" class="form-control" id="type" name="type" value="{{ $resource->type }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $resource->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="{{ $resource->contact }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="link" class="form-label">Lien (optionnel)</label>
                        <input type="url" class="form-control" id="link" name="link" value="{{ $resource->link }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection