<nav class="navbar navbar-expand-lg sticky-top bg-body-tertiary shadow glass">
        <div class="container ps-lg-0">
            <a class="navbar-brand me-5 fw-bold fs-3" href="index.php">TravelMates</a>
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
                    <a class="nav-link small text-body ms-3 pe-4 active" aria-current="page" href="index.php">Home</a>
                    <a class="nav-link small text-body mx-3" href="rooms.php">Rooms</a>
                    <a class="nav-link small text-body mx-3" href="index.php#about">About</a>
                    <button class="btn btn-outline-dark me-2"
                        onclick="location.href='login.php'">Login</button><!--wala pang function-->
                    <button class="nav-link small text-body ms-2 border-0 bg-transparent d-none d-lg-inline"
                        id="mode-lg" type="button" onclick="changeMode()"><i class="bi bi-moon-fill"></i></button>
                </div>
            </div>
        </div>
    </nav>