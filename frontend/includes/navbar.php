<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['userID']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<nav class="navbar navbar-expand-lg sticky-top glass bg-body-tertiary shadow animate-nav">
    <div class="container-fluid px-3 mx-3 px-md-5">
        <a class="navbar-brand fw-bold fs-3" href="/HOTEL-MANAGEMENT-SYSTEM/index.php"><img id="site-logo"
                src="/HOTEL-MANAGEMENT-SYSTEM/images/logo/logoB.png" style="width: 120px;" alt="logo"></a>

        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
            <div class="navbar-nav">
                <a class="nav-link small text-body me-5" href="/HOTEL-MANAGEMENT-SYSTEM/index.php"><i
                        class="bi bi-house-fill me-2"></i>HOME</a>
                <a class="nav-link small text-body me-5" href="/HOTEL-MANAGEMENT-SYSTEM/frontend/rooms.php"><i
                        class="bi bi-door-open me-2"></i>ROOMS</a>
                <a class="nav-link small text-body me-5" href="/HOTEL-MANAGEMENT-SYSTEM/index.php#eventsContainer"><i
                        class="bi bi-calendar-event me-2"></i>EVENTS</a>
                <a class="nav-link small text-body me-5" href="/HOTEL-MANAGEMENT-SYSTEM/index.php#about"><i
                        class="bi bi-info-circle me-2"></i>ABOUT</a>

                <!-- Actions inside collapsed menu on small screens -->
                <div class="d-flex d-lg-none mt-3">
                    <button class="btn btn-outline-dark me-2"
                        onclick="location.href='/HOTEL-MANAGEMENT-SYSTEM/frontend/login.php'">Login</button>
                    <div class="vr mx-2"></div>
                    <button class="nav-link small text-body ms-2 border-0 bg-transparent" id="mode" type="button"
                        onclick="changeMode()"><i class="bi bi-moon-fill"></i></button>
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