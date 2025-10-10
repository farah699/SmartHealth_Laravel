@extends('partials.layouts.master')

@section('title', 'Gestion des Ressources | SmartHealth')
@section('title-sub', 'Admin')
@section('pagetitle', 'Gestion des Ressources d’Aide')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4>Gestion des Ressources</h4>
                <a href="{{ route('resources.create') }}" class="btn btn-light">Ajouter une Ressource</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($resources->isEmpty())
                    <p>Aucune ressource.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Nom</th>
                                <th>Contact</th>
                                <th>Lien</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resources as $resource)
                                <tr>
                                    <td>{{ $resource->type }}</td>
                                    <td>{{ $resource->name }}</td>
                                    <td>{{ $resource->contact }}</td>
                                    <td>{{ $resource->link ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('resources.edit', $resource) }}" class="btn btn-sm btn-warning">Éditer</a>
                                        <form action="{{ route('resources.destroy', $resource) }}" method="POST" style="display:inline;" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger confirm-delete">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intercepter les clics sur les boutons de suppression
    document.querySelectorAll('.confirm-delete').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Empêche la soumission immédiate

            // Trouver le formulaire parent
            const form = this.closest('.delete-form');

            // Afficher SweetAlert2
            Swal.fire({
                title: 'Confirmer la suppression ?',
                text: 'Cette action est irréversible !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire si confirmé
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection