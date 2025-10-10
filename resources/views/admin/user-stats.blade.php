@extends('partials.layouts.master')

@section('title', 'Statistiques Utilisateurs | SmartHealth Admin')
@section('title-sub', 'Administration')
@section('pagetitle', 'Tableau de Bord des Utilisateurs')

@section('css')
    {{-- List.js is great for simple client-side search/sort on tables --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js">
    <style>
        .stat-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .table-responsive .table {
            min-width: 800px;
        }
        .table-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        .table-initials {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .search {
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            width: 100%;
        }
    </style>
@endsection

@section('content')
<div id="layout-wrapper">

    <!-- Section des statistiques -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-people-fill fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0 text-white">{{ $totalUsers }}</h5>
                        <span class="opacity-75">Utilisateurs Totaux</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-person-check-fill fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0 text-white">{{ $enabledUsers }}</h5>
                        <span class="opacity-75">Comptes Activés</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-mortarboard-fill fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0 text-white">{{ $studentCount }}</h5>
                        <span class="opacity-75">Étudiants</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-person-video3 fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0 text-dark">{{ $teacherCount }}</h5>
                        <span class="opacity-75">Professeurs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des graphiques -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Inscriptions des 30 derniers jours</h5>
                </div>
                <div class="card-body">
                    <div id="registrationsChart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Répartition par Rôle</h5>
                </div>
                <div class="card-body">
                    <div id="rolesChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section de la table des utilisateurs -->
    <div class="row">
        <div class="col-12">
            <div class="card" id="users-table">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0">Liste des Utilisateurs</h5>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control search" placeholder="Rechercher..." style="max-width: 200px;"/>
                        <a href="#" class="btn btn-primary"><i class="ri-add-line me-1"></i> Ajouter</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="sort" data-sort="user-name">Nom</th>
                                    <th class="sort" data-sort="user-email">Email</th>
                                    <th class="sort" data-sort="user-role">Rôle</th>
                                    <th class="sort" data-sort="user-status">Statut</th>
                                    <th class="sort" data-sort="user-date">Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="user-name">
                                            <div class="d-flex align-items-center">
                                                @if($user->avatar)
                                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="table-avatar rounded-circle me-2">
                                                @else
                                                    <div class="table-initials rounded-circle bg-primary-subtle text-primary me-2">
                                                        {{ $user->initials }}
                                                    </div>
                                                @endif
                                                <span>{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="user-email">{{ $user->email }}</td>
                                        <td class="user-role">
                                            @if($user->role === 'Teacher')
                                                <span class="badge bg-warning-subtle text-warning">{{ $user->role }}</span>
                                            @else
                                                <span class="badge bg-info-subtle text-info">{{ $user->role }}</span>
                                            @endif
                                        </td>
                                        <td class="user-status">
                                            @if($user->enabled)
                                                <span class="badge bg-success-subtle text-success">Activé</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Désactivé</span>
                                            @endif
                                        </td>
                                        <td class="user-date">{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Bouton Activer/Désactiver -->
                                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm {{ $user->enabled ? 'btn-outline-secondary' : 'btn-outline-success' }}" data-bs-toggle="tooltip" title="{{ $user->enabled ? 'Désactiver' : 'Activer' }}">
                                                        <i class="bi {{ $user->enabled ? 'bi-toggle-off' : 'bi-toggle-on' }}"></i>
                                                    </button>
                                                </form>
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Supprimer">
                                                        <i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun utilisateur trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <ul class="pagination"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Chart: Répartition par rôle (Pie Chart)
    var roleChartOptions = {
        series: [{{ $studentCount }}, {{ $teacherCount }}],
        chart: {
            type: 'pie',
            height: 350
        },
        labels: ['Étudiants', 'Professeurs'],
        colors: ['#008FFB', '#FEB019'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    var roleChart = new ApexCharts(document.querySelector("#rolesChart"), roleChartOptions);
    roleChart.render();

    // Chart: Inscriptions (Bar Chart)
    var registrationChartOptions = {
        series: [{
            name: 'Nouveaux utilisateurs',
            data: @json($registrationData)
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: @json($registrationLabels),
        },
        yaxis: {
            title: {
                text: 'Nombre d\'inscriptions'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " utilisateurs"
                }
            }
        }
    };
    var registrationChart = new ApexCharts(document.querySelector("#registrationsChart"), registrationChartOptions);
    registrationChart.render();

    // Table search and pagination using List.js
    var options = {
        valueNames: [ 'user-name', 'user-email', 'user-role', 'user-status', 'user-date' ],
        page: 10,
        pagination: true
    };
    var userList = new List('users-table', options);
});
</script>
@endsection