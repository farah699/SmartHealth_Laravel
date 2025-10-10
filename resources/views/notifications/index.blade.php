@extends('partials.layouts.master')

@section('title', 'Notifications | SmartHealth')
@section('title-sub', 'Notifications')
@section('pagetitle', 'Mes Notifications')

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mes Notifications</h5>
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="ri-check-double-line me-1"></i>Tout marquer comme lu
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item p-3 border-bottom {{ $notification->is_read ? 'bg-light' : 'bg-white' }}">
                            <div class="d-flex gap-3">
                                @if($notification->sender->avatar)
                                    <img src="{{ $notification->sender->avatar_url }}" alt="Avatar"
                                         class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" 
                                         style="width: 48px; height: 48px;">
                                        {{ $notification->sender->initials }}
                                    </div>
                                @endif
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                                {{ $notification->title }}
                                            </h6>
                                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="ri-time-line me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary">Nouveau</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-2">
                                        <a href="{{ route('notifications.markAsRead', $notification->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ri-eye-line me-1"></i>Voir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ri-notification-off-line fs-48 text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune notification</h5>
                            <p class="text-muted">Vous n'avez pas encore de notifications.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection