<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['userID']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<nav class="navbar navbar-expand-lg sticky-top glass bg-body-tertiary shadow animate-nav">
        <div class="container ps-lg-0">
            <a class="navbar-brand me-5 fw-bold fs-3" href="/HOTEL-MANAGEMENT-SYSTEM/index.php">TravelMates</a>
            <div class="d-flex d-lg-none align-items-center">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavAltMarkup" aria-controls="#navbarNavAltMarkup" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <button class="nav-link small text-body ms-3 me-2 border-0 bg-transparent" id="mode" type="button"
                    onclick="changeMode()"><i class="bi bi-moon-fill"></i></button>
            </div>
            <button class="navbar-toggler d-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavAltMarkup" aria-controls="#navbarNavAltMarkup" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link small text-body ms-3 pe-4 active" href="/HOTEL-MANAGEMENT-SYSTEM/index.php">Home</a>
                    <a class="nav-link small text-body mx-3" href="/HOTEL-MANAGEMENT-SYSTEM/frontend/rooms.php">Rooms</a>
                    <a class="nav-link small text-body mx-3" href="/HOTEL-MANAGEMENT-SYSTEM/index.php#eventsContainer">Events</a>
                    <a class="nav-link small text-body mx-3" href="/HOTEL-MANAGEMENT-SYSTEM/index.php#about">About</a>
                    
                    <?php if ($isLoggedIn): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle me-2" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($username); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="/HOTEL-MANAGEMENT-SYSTEM/frontend/bookings.php"><i class="bi bi-calendar-check me-2"></i>My Bookings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/HOTEL-MANAGEMENT-SYSTEM/frontend/php/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-outline-dark me-2"
                            onclick="location.href='/HOTEL-MANAGEMENT-SYSTEM/frontend/login.php'">Login</button>
                    <?php endif; ?>
                    
                    <button class="nav-link small text-body ms-2 border-0 bg-transparent d-none d-lg-inline"
                        id="mode-lg" type="button" onclick="changeMode()"><i class="bi bi-moon-fill"></i></button>
                </div>
            </div>
        </div>

        <div class="d-none d-lg-flex align-items-center ms-auto">
            <button class="btn btn-outline-dark me-2"
                onclick="location.href='/HOTEL-MANAGEMENT-SYSTEM/frontend/login.php'">Login</button>
            <div class="vr mx-2"></div>
            <button class="nav-link small text-body ms-2 border-0 bg-transparent d-none d-lg-inline" id="mode-lg"
                type="button" onclick="changeMode()"><i class="bi bi-moon-fill"></i></button>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stored = localStorage.getItem('siteMode');
        const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
        if (stored && stored !== current) {
            if (typeof changeMode === 'function') {
                changeMode();
                var logoAfter = document.getElementById('site-logo');
                if (logoAfter) logoAfter.src = stored === 'dark' ? '/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoW.png' : '/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoB.png';
            } else {
                document.documentElement.setAttribute('data-bs-theme', stored);
                document.querySelectorAll('#mode i, #mode-lg i').forEach(function (icon) {
                    icon.className = stored === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
                });
                document.querySelectorAll('.text-black, .text-white').forEach(function (el) {
                    el.classList.toggle('text-black');
                    el.classList.toggle('text-white');
                });
                document.querySelectorAll('.btn-outline-dark, .btn-outline-light').forEach(function (el) {
                    el.classList.toggle('btn-outline-dark');
                    el.classList.toggle('btn-outline-light');
                });
                function applyLogo(theme) {
                    var logo = document.getElementById('site-logo');
                    if (!logo) return;
                    logo.src = theme === 'dark' ? '/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoW.png' : '/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoB.png';
                }
                applyLogo(stored);
            }
        }
        function updateStoredModeAndLogo() {
            setTimeout(function () {
                const now = document.documentElement.getAttribute('data-bs-theme') || 'light';
                localStorage.setItem('siteMode', now);
                var logo = document.getElementById('site-logo');
                if (logo) logo.src = now === 'dark' ? '/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoW.png' : '/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoB.png';
            }, 10);
        }
        document.querySelectorAll('#mode, #mode-lg').forEach(function (btn) {
            btn.addEventListener('click', updateStoredModeAndLogo);
        });
    });
</script>