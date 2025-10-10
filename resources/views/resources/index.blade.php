@extends('partials.layouts.master')

@section('title', 'Ressources d’Aide | SmartHealth')
@section('title-sub', 'Aide et Contacts d’Urgence')
@section('pagetitle', 'Ressources d’Aide et Contacts d’Urgence')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-info text-white">
                <h4>Numéros Utiles et Contacts d’Urgence</h4>
                <p>Accès rapide à une aide réelle en cas de crise.</p>
            </div>
            <div class="card-body">
                @if($resources->isEmpty())
                    <p>Aucune ressource disponible pour le moment.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Nom</th>
                                <th>Contact</th>
                                <th>Lien</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resources as $resource)
                                <tr>
                                    <td>{{ $resource->type }}</td>
                                    <td>{{ $resource->name }}</td>
                                    <td>{{ $resource->contact }}</td>
                                    <td>
                                        @if($resource->link)
                                            <a href="{{ $resource->link }}" target="_blank">Visiter</a>
                                        @else
                                            -
                                        @endif
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