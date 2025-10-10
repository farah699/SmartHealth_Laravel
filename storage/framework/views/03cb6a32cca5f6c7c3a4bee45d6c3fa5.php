<!-- Begin Header -->
<header class="app-header" id="appHeader">
    <div class="container-fluid w-100">
        <div class="d-flex align-items-center">
            <!-- Section gauche : Logo et menu principal -->
            <div class="me-auto">
                <div class="d-inline-flex align-items-center gap-5">
                    <!-- Logo -->
                    <a href="<?php echo e(url('index')); ?>" class="fs-18 fw-semibold">
                        <img height="30" class="header-sidebar-logo-default d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-dark.png')); ?>">
                        <img height="30" class="header-sidebar-logo-light d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-light.png')); ?>">
                        <img height="30" class="header-sidebar-logo-small d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-md.png')); ?>">
                        <img height="30" class="header-sidebar-logo-small-light d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-md-light.png')); ?>">
                    </a>
                    <a href="<?php echo e(url('index')); ?>" class="fs-18 fw-semibold">
                        <img height="30" class="header-sidebar-logo-default d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-dark.png')); ?>">
                        <img height="30" class="header-sidebar-logo-light d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-light.png')); ?>">
                        <img height="30" class="header-sidebar-logo-small d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-md.png')); ?>">
                        <img height="30" class="header-sidebar-logo-small-light d-none" alt="Logo" src="<?php echo e(asset('assets/images/logo-md-light.png')); ?>">
                    </a>

                    <!-- Boutons de bascule (sidebar/horizontal) -->
                    <button type="button" class="vertical-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill" id="toggleSidebar">
                        <i class="bi bi-arrow-bar-left header-icon"></i>
                    </button>
                    <button type="button" class="horizontal-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill d-none" id="toggleHorizontal">
                        <i class="ri-menu-2-line header-icon"></i>
                    </button>

                    <!-- Dropdowns de navigation -->
                    <div class="header-dropdown d-flex align-items-center">
                        <!-- Dropdown "About" -->
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                About
                            </button>
                            <div class="dropdown-menu dropdown-mega-xl p-0">
                                <div class="p-4 border-bottom d-flex align-items-center gap-4">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fs-15">Ready to Begin Your Journey?</h6>
                                        <p class="mb-0 text-muted">Explore our resources to kickstart your experience!</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary">Documentation</a>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Apps</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="apps-chat">Chat</a></li>
                                                <li><a class="dropdown-item" href="apps-calendar">Calendar</a></li>
                                                <li><a class="dropdown-item" href="apps-email">Mailbox</a></li>
                                                <li><a class="dropdown-item" href="dashboard-ecommerce">Ecommerce</a></li>
                                                <li><a class="dropdown-item" href="index">Academy</a></li>
                                                <li><a class="dropdown-item" href="dashboard-logistic">Logistics</a></li>
                                                <li><a class="dropdown-item" href="dashboard-crm">CRM</a></li>
                                                <li><a class="dropdown-item" href="dashboard-project">Projects</a></li>
                                                <li><a class="dropdown-item" href="apps-invoice-detail">Invoices</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Pages</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>">Profile</a></li>
                                                <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>">Profile</a></li>
                                                <li><a class="dropdown-item" href="../pages-timeline">Timeline</a></li>
                                                <li><a class="dropdown-item" href="../pages-blog-list">Blogs</a></li>
                                                <li><a class="dropdown-item" href="../pages-pricing">Pricing</a></li>
                                                <li><a class="dropdown-item" href="../pages-gallery">Gallery</a></li>
                                                <li><a class="dropdown-item" href="../pages-faqs">FAQ's</a></li>
                                                <li><a class="dropdown-item" href="../pages-sitemap">Sitemap</a></li>
                                                <li><a class="dropdown-item" href="../pages-search-result">Search Results</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Authentication & Error</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="../auth-signup">Sign Up</a></li>
                                                <li><a class="dropdown-item" href="../auth-signin">Sign In</a></li>
                                                <li><a class="dropdown-item" href="../auth-two-step-verify">Two Step Verification</a></li>
                                                <li><a class="dropdown-item" href="../auth-reset-password">Reset Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-create-password">Create Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-404">404</a></li>
                                                <li><a class="dropdown-item" href="../under-maintenance">Maintenance</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown "Authentication & Pages" -->
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Authentication & Pages
                            </button>
                            <div class="dropdown-menu dropdown-mega-lg p-0">
                                <div class="p-4 d-flex align-items-center gap-4 bg-primary">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fs-15 text-bg-primary">Ready to Begin Your Journey?</h6>
                                        <p class="mb-0 text-white text-opacity-75">Explore our resources to kickstart your experience!</p>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Apps</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="apps-chat">Chat</a></li>
                                                <li><a class="dropdown-item" href="apps-calendar">Calendar</a></li>
                                                <li><a class="dropdown-item" href="apps-email">Mailbox</a></li>
                                                <li><a class="dropdown-item" href="dashboard-ecommerce">Ecommerce</a></li>
                                                <li><a class="dropdown-item" href="index">Academy</a></li>
                                                <li><a class="dropdown-item" href="dashboard-logistic">Logistics</a></li>
                                                <li><a class="dropdown-item" href="dashboard-crm">CRM</a></li>
                                                <li><a class="dropdown-item" href="dashboard-project">Projects</a></li>
                                                <li><a class="dropdown-item" href="apps-invoice-detail">Invoices</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Pages</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>">Profile</a></li>
                                                <li><a class="dropdown-item" href="../pages-timeline">Timeline</a></li>
                                                <li><a class="dropdown-item" href="../pages-blog-list">Blogs</a></li>
                                                <li><a class="dropdown-item" href="../pages-pricing">Pricing</a></li>
                                                <li><a class="dropdown-item" href="../pages-gallery">Gallery</a></li>
                                                <li><a class="dropdown-item" href="../pages-faqs">FAQ's</a></li>
                                                <li><a class="dropdown-item" href="../pages-sitemap">Sitemap</a></li>
                                                <li><a class="dropdown-item" href="../pages-search-result">Search Results</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Authentication & Error</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="../auth-signup">Sign Up</a></li>
                                                <li><a class="dropdown-item" href="../auth-signin">Sign In</a></li>
                                                <li><a class="dropdown-item" href="../auth-two-step-verify">Two Step Verification</a></li>
                                                <li><a class="dropdown-item" href="../auth-reset-password">Reset Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-create-password">Create Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-404">404</a></li>
                                                <li><a class="dropdown-item" href="../under-maintenance">Maintenance</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown "Help" -->
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Help
                            </button>
                            <div class="dropdown-menu p-4">
                                <p class="mb-1 text-uppercase text-muted fs-12">Help & Support</p>
                                <ul class="list-unstyled mb-0">
                                    <li><a class="dropdown-item" href="javascript:void(0)">Documentation</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)">Quick Support</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)">Buy Now</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section droite : Boutons et profil -->
            <div class="flex-shrink-0 d-flex align-items-center gap-1">
                <!-- Bouton de recherche -->
                <button type="button" class="btn header-btn d-none d-md-block" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">
                    <i class="bi bi-search"></i>
                </button>

                <!-- Bouton de configuration -->
                <button class="btn header-btn d-none d-md-block" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="bi bi-gear"></i>
                </button>

                <!-- Mode clair/sombre -->
                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn">
                        <i class="bi bi-brightness-high"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>

                <!-- Dropdown Notifications -->
<!-- Remplacer la section notifications existante par : -->
<?php if(auth()->guard()->check()): ?>
<!-- Dropdown Notifications AMÉLIORÉ -->
<div class="dropdown pe-dropdown-mega d-none d-md-block">
    <button class="btn header-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBtn">
        <i class="bi bi-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
              id="notificationCount" style="display: none;">
            0
        </span>
    </button>
    <div class="dropdown-menu dropdown-mega-md header-dropdown-menu pe-noti-dropdown-menu p-0" id="notificationDropdown">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                Notifications 
                <span class="badge bg-success rounded-circle align-middle ms-1" id="notificationBadge">0</span>
            </h6>
            <button class="btn btn-sm btn-link text-primary p-0" onclick="markAllAsRead()">
                Tout marquer comme lu
            </button>
        </div>
        <div class="p-3" id="notificationList">
            <div class="text-center py-4" id="noNotifications">
                <i class="ri-notification-off-line fs-48 text-muted mb-3"></i>
                <p class="text-muted">Aucune notification</p>
            </div>
        </div>
        <div class="border-top p-3 text-center">
            <a href="<?php echo e(route('notifications.index')); ?>" class="text-primary">Voir toutes les notifications</a>
        </div>
    </div>
</div>

<script>
// Charger les notifications au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Recharger les notifications toutes les 30 secondes
    setInterval(loadNotifications, 30000);
});

// Fonction pour charger les notifications
function loadNotifications() {
    fetch('/notifications/get')
        .then(response => response.json())
        .then(data => {
            updateNotificationUI(data);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des notifications:', error);
        });
}

// Fonction pour mettre à jour l'interface utilisateur
function updateNotificationUI(data) {
    const count = data.count;
    const notifications = data.notifications;
    
    // Mettre à jour le compteur
    const countBadge = document.getElementById('notificationCount');
    const badge = document.getElementById('notificationBadge');
    
    if (count > 0) {
        countBadge.textContent = count;
        countBadge.style.display = 'inline';
        badge.textContent = count;
        badge.classList.remove('bg-secondary');
        badge.classList.add('bg-success');
    } else {
        countBadge.style.display = 'none';
        badge.textContent = '0';
        badge.classList.remove('bg-success');
        badge.classList.add('bg-secondary');
    }
    
    // Mettre à jour la liste des notifications
    const notificationList = document.getElementById('notificationList');
    const noNotifications = document.getElementById('noNotifications');
    
    if (notifications.length > 0) {
        noNotifications.style.display = 'none';
        notificationList.innerHTML = notifications.map(notification => `
            <div class="noti-item" onclick="markAsRead(${notification.id})">
                <img src="${notification.sender_avatar || '/assets/images/avatar/default.png'}" alt="Avatar" class="avatar-md">
                <div>
                    <a href="javascript:void(0)" class="stretched-link">
                        <h6 class="mb-1">${notification.title}</h6>
                    </a>
                    <p class="text-muted mb-1 fs-12">${notification.message}</p>
                    <p class="text-muted mb-0 fs-11">${notification.created_at}</p>
                </div>
            </div>
        `).join('');
    } else {
        noNotifications.style.display = 'block';
        notificationList.innerHTML = '<div id="noNotifications" class="text-center py-4"><i class="ri-notification-off-line fs-48 text-muted mb-3"></i><p class="text-muted">Aucune notification</p></div>';
    }
}

// Fonction pour marquer une notification comme lue
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.ok) {
            // Redirection sera gérée par le serveur
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Fonction pour marquer toutes les notifications comme lues
function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications(); // Recharger les notifications
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}
</script>
<?php endif; ?>

                <!-- Dropdown Panier -->
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn btn-icon header-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cart"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-mega-md header-dropdown-menu p-0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Cart Items</h5>
                                <a href="javascript:void(0)" id="cart-data">5 Item</a>
                            </div>
                            <ul class="list-unstyled list-none mb-0 p-4" id="header-cart-items-scroll">
                                <li class="dropdown-item d-block py-2">
                                    <div class="d-flex items-start cart-dropdown-item">
                                        <img src="assets/images/product/img-01.png" class="avatar-md me-3" alt="img">
                                        <div class="flex-grow-1">
                                            <div>
                                                <h6><a href="apps-ecommerce-products-details" class="text-reset">Branded Crop Top</a></h6>
                                                <p class="mb-0 fs-12 text-muted">Quantity: <span>1 x $499</span></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center px-2">
                                            <h6 class="m-0 fw-normal">$<span class="cart-item-price">499</span></h6>
                                        </div>
                                        <div class="ps-2 d-flex">
                                            <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-item d-block py-2">
                                    <div class="d-flex items-start cart-dropdown-item">
                                        <img src="assets/images/product/img-02.png" class="avatar-md me-3" alt="img">
                                        <div class="flex-grow-1">
                                            <div>
                                                <h6><a href="apps-ecommerce-products-details" class="text-reset">Stop Watch</a></h6>
                                                <p class="mb-0 fs-12 text-muted">Quantity: <span>2 x $159</span></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center px-2">
                                            <h6 class="m-0 fw-normal">$<span class="cart-item-price">318</span></h6>
                                        </div>
                                        <div class="ps-2 d-flex">
                                            <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-item d-block py-2">
                                    <div class="d-flex items-start cart-dropdown-item">
                                        <img src="assets/images/product/img-03.png" class="avatar-md me-3" alt="img">
                                        <div class="flex-grow-1">
                                            <div>
                                                <h6><a href="apps-ecommerce-products-details" class="text-reset">Jeens Shoes</a></h6>
                                                <p class="mb-0 fs-12 text-muted">Quantity: <span>6 x $399</span></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center px-2">
                                            <h6 class="m-0 fw-normal">$<span class="cart-item-price">2394</span></h6>
                                        </div>
                                        <div class="ps-2 d-flex">
                                            <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-item d-block py-2">
                                    <div class="d-flex items-start cart-dropdown-item">
                                        <img src="assets/images/product/img-04.png" class="avatar-md me-3" alt="img">
                                        <div class="flex-grow-1">
                                            <div>
                                                <h6><a href="apps-ecommerce-products-details" class="text-reset">Solder Less T-shirt</a></h6>
                                                <p class="mb-0 fs-12 text-muted">Quantity: <span>3 x $259</span></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center px-2">
                                            <h6 class="m-0 fw-normal">$<span class="cart-item-price">777</span></h6>
                                        </div>
                                        <div class="ps-2 d-flex">
                                            <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-item d-block py-2">
                                    <div class="d-flex items-start cart-dropdown-item">
                                        <img src="assets/images/product/img-05.png" class="avatar-md me-3" alt="img">
                                        <div class="flex-grow-1">
                                            <div>
                                                <h6><a href="apps-ecommerce-products-details" class="text-reset">Mens T-shirt</a></h6>
                                                <p class="mb-0 fs-12 text-muted">Quantity: <span>2 x $299</span></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center px-2">
                                            <h6 class="m-0 fw-normal">$<span class="cart-item-price">598</span></h6>
                                        </div>
                                        <div class="ps-2 d-flex">
                                            <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="px-4 text-end">
                                <a href="apps-ecommerce-cart"><button class="btn btn-outline-primary" type="button">View Cart</button></a>
                                <a class="btn btn-primary view-checkout" href="apps-ecommerce-checkout">Checkout </a>
                            </div>
                        </div>
                    </ul>
                </div>

                <!-- Dropdown Langues -->
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn btn-icon header-btn p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                    </button>
                    <ul class="dropdown-menu header-dropdown-menu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/es.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Spanish
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/ru.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                French
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Russian
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/de.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                German
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/cn.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Chinese
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/sa.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Arabic
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Dropdown Profil AMÉLIORÉ -->
                <?php if(auth()->guard()->check()): ?>
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="header-profile-btn btn gap-1 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-btn btn position-relative">
                            <?php if(Auth::user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . Auth::user()->avatar)); ?>" alt="Avatar Image" class="img-fluid rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-white fs-12 fw-bold"><?php echo e(substr(Auth::user()->name, 0, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <span class="position-absolute translate-middle badge border border-light rounded-circle bg-success"><span class="visually-hidden">unread messages</span></span>
                        </span>
                        <div class="d-none d-lg-block pe-2">
                            <span class="d-block mb-0 fs-13 fw-semibold"><?php echo e(Str::limit(Auth::user()->name, 15)); ?></span>
                            <span class="d-block mb-0 fs-12 text-muted">
                                <?php if(Auth::user()->bio): ?>
                                    <?php echo e(Str::limit(Auth::user()->bio, 20)); ?>

                                <?php else: ?>
                                    Membre SmartHealth
                                <?php endif; ?>
                            </span>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-mega-sm header-dropdown-menu p-3">
                        <!-- En-tête du profil -->
                        <div class="border-bottom pb-2 mb-2 d-flex align-items-center gap-2">
                            <?php if(Auth::user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . Auth::user()->avatar)); ?>" alt="Avatar Image" class="avatar-md rounded-circle" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="avatar-md rounded-circle bg-primary d-flex align-items-center justify-content-center">
                                    <span class="text-white fs-16 fw-bold"><?php echo e(substr(Auth::user()->name, 0, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="<?php echo e(route('profile.show')); ?>">
                                    <h6 class="mb-0 lh-base"><?php echo e(Auth::user()->name); ?></h6>
                                </a>
                                
                            </div>
                        </div>

                        <!-- Menu principal du profil -->
                        <ul class="list-unstyled mb-1 border-bottom pb-1">
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>">
                                    <i class="bi bi-person me-1"></i> Voir mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                                    <i class="bi bi-pencil me-1"></i> Modifier le profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>#profile-password">
                                    <i class="bi bi-key me-1"></i> Changer le mot de passe
                                </a>
                            </li>
                        </ul>

                        <!-- Menu secondaire -->
                        <ul class="list-unstyled mb-1 border-bottom pb-1">
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-bell me-1"></i> Notifications</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-gear me-1"></i> Paramètres</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-shield-check me-1"></i> Sécurité</a></li>
                        </ul>

                        <!-- Menu d'aide -->
                        <ul class="list-unstyled mb-1 border-bottom pb-1">
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-question-circle me-1"></i> Centre d'aide</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-chat-dots me-1"></i> Support</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-info-circle me-1"></i> À propos</a></li>
                        </ul>

               
                        <!-- Déconnexion avec SweetAlert2 -->
                        <ul class="list-unstyled mb-0">
                            <li>
                                <button type="button" class="dropdown-item text-danger" id="logoutBtn" style="background: none; border: none; width: 100%; text-align: left;">
                                    <i class="bi bi-box-arrow-right me-1"></i> Se déconnecter
                                </button>
                            </li>
                        </ul>

                        <!-- Informations de compte en bas -->
                        <div class="border-top pt-2 mt-2 text-center">
                            <small class="text-muted">
                                Membre depuis <?php echo e(Auth::user()->created_at->format('M Y')); ?>

                                <?php if(Auth::user()->birth_date): ?>
                                    <br><?php echo e(Auth::user()->birth_date->age); ?> ans
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Si non connecté, bouton de connexion -->
                <div class="d-flex align-items-center gap-2">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-primary btn-sm d-none d-lg-inline-block">
                        S'inscrire
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<!-- END Header -->

<!-- Formulaire de déconnexion masqué -->
<?php if(auth()->guard()->check()): ?>
<form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
</form>
<?php endif; ?>

<!-- Scripts SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if(auth()->guard()->check()): ?>
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Êtes-vous sûr de vouloir vous déconnecter ?',
                text: "Vous devrez vous reconnecter pour accéder à votre compte.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'rgba(237, 127, 24, 1)',
                cancelButtonColor: '#020a12ff',
                confirmButtonText: 'oui',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });
    }
});
<?php endif; ?>
</script>
<!-- END Header -->

<!-- Modal de recherche -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 bg-transparent">
            <div class="d-flex justify-content-between align-items-center bg-body">
                <div class="d-flex align-items-center border-0 px-3">
                    <i class="bi bi-search me-2"></i>
                    <input class="d-flex w-full py-3 bg-transparent border-0 focus-ring" placeholder="Rechercher..." autocomplete="off" autocorrect="off" spellcheck="false" aria-autocomplete="list" role="combobox" aria-expanded="true" type="text">
                </div>
                <button type="button" class="btn-close pe-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body mt-4">
                <p class="font-normal mb-2">Recherches récentes...</p>
                <span class="badge bg-light-subtle border text-body">Mon profil <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Paramètres <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Dashboard <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Notifications <i class="ri-close-line"></i></span>
            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/resources/views/partials/header.blade.php ENDPATH**/ ?>