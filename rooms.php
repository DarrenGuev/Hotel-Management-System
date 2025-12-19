<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - Rooms</title>
    <link rel="icon" type="image/png" href="images/flag.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .room-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        @media (min-width: 768px) {
            .room-card-img {
                height: 100%;
                min-height: 200px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-12 col-lg-3 col-xl-2 bg-body-tertiary min-vh-100 border-end p-4">
                <h5 class="fw-bold mb-4">Filter Rooms</h5>

                <!-- Room Type -->
                <div class="border-bottom pb-3 mb-3">
                    <h6 class="fw-semibold mb-3 text-secondary">Room Type</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="basic" id="typeBasic">
                        <label class="form-check-label small" for="typeBasic">Basic</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="twin" id="typeTwin">
                        <label class="form-check-label small" for="typeTwin">Twin</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="deluxe" id="typeDeluxe">
                        <label class="form-check-label small" for="typeDeluxe">Deluxe</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="single" id="typeSingle">
                        <label class="form-check-label small" for="typeSingle">Single</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="family" id="typeFamily">
                        <label class="form-check-label small" for="typeFamily">Family</label>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="border-bottom pb-3 mb-3">
                    <h6 class="fw-semibold mb-3 text-secondary">Price Range (₱)</h6>
                    <input type="range" class="form-range" min="1000" max="6000" step="100" id="priceRange"
                        value="3500">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>₱1,000</span>
                        <span>₱6,000</span>
                    </div>
                </div>

                <!-- Facilities -->
                <div class="border-bottom pb-3 mb-3">
                    <h6 class="fw-semibold mb-3 text-secondary">Facilities</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="wifi" id="facilityWifi">
                        <label class="form-check-label small" for="facilityWifi">WiFi</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="aircon" id="facilityAircon">
                        <label class="form-check-label small" for="facilityAircon">Air-conditioner</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="tv" id="facilityTv">
                        <label class="form-check-label small" for="facilityTv">Television</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="kitchen" id="facilityKitchen">
                        <label class="form-check-label small" for="facilityKitchen">Kitchen</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="parking" id="facilityParking">
                        <label class="form-check-label small" for="facilityParking">Parking</label>
                    </div>
                </div>

                <!-- Guest Capacity -->
                <div class="pb-3 mb-3">
                    <h6 class="fw-semibold mb-3 text-secondary">Guest Capacity</h6>
                    <select class="form-select" id="guestCapacity">
                        <option value="">Any</option>
                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="5">5+ Guests</option>
                    </select>
                </div>
            </div>

            <!-- Room Listings -->
            <div class="col-12 col-lg-9 col-xl-10 p-4">
                <h2 class="text-center fw-bold mb-4 fst-italic mt-5">Recommended Rooms</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;"></div>

                <div class="container">
                    <div class="row mt-5">
                        <div class="col">
                            <h2 class="fw-bold mb-3">
                                Basic Room
                            </h2>
                        </div>
                    </div>
                    <div class="row" id="basicRoomCards">
                        <!-- Basic Room Card -->

                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                            <div class="card h-100 bg-transparent shadow rounded-3">
                                <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                                    <img src="images/rooms/basic.jpeg" class="card-img-top img-fluid" alt="Basic Room">
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1">Basic Room</h5>
                                    <p class="text-secondary fst-italic small mb-2">Comfort Meets Simplicity</p>
                                    <p class="fw-semibold mb-3">₱1,389 / night</p>
                                    <div class="mb-2">
                                        <span class="badge bg-dark me-1 mb-1">1 Room</span>
                                        <span class="badge bg-dark me-1 mb-1">1 Bathroom</span>
                                        <span class="badge bg-dark me-1 mb-1">Hot/Cold Shower</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-dark me-1 mb-1">Wifi</span>
                                        <span class="badge bg-dark me-1 mb-1">Air-conditioner</span>
                                        <span class="badge bg-dark me-1 mb-1">Television</span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-warning">Book Now</button>
                                        <button class="btn btn-outline-secondary">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row mt-5">
                        <div class="col">
                            <h2 class="fw-bold mb-3">
                                Twin Room
                            </h2>
                        </div>
                    </div>
                    <div class="row" id="twinRoomCards">
                        <!-- Basic Room Card -->
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                            <div class="card h-100 bg-transparent shadow rounded-3">
                                <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                                    <img src="images/rooms/basic.jpeg" class="card-img-top img-fluid" alt="Basic Room">
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1">Basic Room</h5>
                                    <p class="text-secondary fst-italic small mb-2">Comfort Meets Simplicity</p>
                                    <p class="fw-semibold mb-3">₱1,389 / night</p>
                                    <div class="mb-2">
                                        <span class="badge bg-dark me-1 mb-1">1 Room</span>
                                        <span class="badge bg-dark me-1 mb-1">1 Bathroom</span>
                                        <span class="badge bg-dark me-1 mb-1">Hot/Cold Shower</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-dark me-1 mb-1">Wifi</span>
                                        <span class="badge bg-dark me-1 mb-1">Air-conditioner</span>
                                        <span class="badge bg-dark me-1 mb-1">Television</span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-warning">Book Now</button>
                                        <button class="btn btn-outline-secondary">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row mt-5">
                        <div class="col">
                            <h2 class="fw-bold mb-3">
                                Deluxe Room
                            </h2>
                        </div>
                    </div>
                    <div class="row" id="deluxeRoomCards">
                        <!-- Deluxe Room Card -->
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                            <div class="card h-100 bg-transparent shadow rounded-3">
                                <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                                    <img src="images/rooms/basic.jpeg" class="card-img-top img-fluid" alt="Basic Room">
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1">Basic Room</h5>
                                    <p class="text-secondary fst-italic small mb-2">Comfort Meets Simplicity</p>
                                    <p class="fw-semibold mb-3">₱1,389 / night</p>
                                    <div class="mb-2">
                                        <span class="badge bg-dark me-1 mb-1">1 Room</span>
                                        <span class="badge bg-dark me-1 mb-1">1 Bathroom</span>
                                        <span class="badge bg-dark me-1 mb-1">Hot/Cold Shower</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-dark me-1 mb-1">Wifi</span>
                                        <span class="badge bg-dark me-1 mb-1">Air-conditioner</span>
                                        <span class="badge bg-dark me-1 mb-1">Television</span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-warning">Book Now</button>
                                        <button class="btn btn-outline-secondary">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row mt-5">
                        <div class="col">
                            <h2 class="fw-bold mb-3">
                                Single Room
                            </h2>
                        </div>
                    </div>
                    <div class="row" id="singleRoomCards">
                        <!-- Single Room Card -->
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                            <div class="card h-100 bg-transparent shadow rounded-3">
                                <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                                    <img src="images/rooms/basic.jpeg" class="card-img-top img-fluid" alt="Basic Room">
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1">Basic Room</h5>
                                    <p class="text-secondary fst-italic small mb-2">Comfort Meets Simplicity</p>
                                    <p class="fw-semibold mb-3">₱1,389 / night</p>
                                    <div class="mb-2">
                                        <span class="badge bg-dark me-1 mb-1">1 Room</span>
                                        <span class="badge bg-dark me-1 mb-1">1 Bathroom</span>
                                        <span class="badge bg-dark me-1 mb-1">Hot/Cold Shower</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-dark me-1 mb-1">Wifi</span>
                                        <span class="badge bg-dark me-1 mb-1">Air-conditioner</span>
                                        <span class="badge bg-dark me-1 mb-1">Television</span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-warning">Book Now</button>
                                        <button class="btn btn-outline-secondary">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row mt-5">
                        <div class="col">
                            <h2 class="fw-bold mb-3">
                                Family Room
                            </h2>
                        </div>
                    </div>
                    <div class="row" id="familyRoomCards">
                        <!-- Family Room Card -->
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                            <div class="card h-100 bg-transparent shadow rounded-3">
                                <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                                    <img src="images/rooms/basic.jpeg" class="card-img-top img-fluid" alt="Basic Room">
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1">Basic Room</h5>
                                    <p class="text-secondary fst-italic small mb-2">Comfort Meets Simplicity</p>
                                    <p class="fw-semibold mb-3">₱1,389 / night</p>
                                    <div class="mb-2">
                                        <span class="badge bg-dark me-1 mb-1">1 Room</span>
                                        <span class="badge bg-dark me-1 mb-1">1 Bathroom</span>
                                        <span class="badge bg-dark me-1 mb-1">Hot/Cold Shower</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-dark me-1 mb-1">Wifi</span>
                                        <span class="badge bg-dark me-1 mb-1">Air-conditioner</span>
                                        <span class="badge bg-dark me-1 mb-1">Television</span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-warning">Book Now</button>
                                        <button class="btn btn-outline-secondary">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        function changeMode() {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
            document.querySelectorAll('#mode i, #mode-lg i').forEach(icon => {
                icon.className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
            });

            document.querySelectorAll('.bg-dark, .bg-secondary').forEach(element => {
                element.classList.toggle('bg-dark');
                element.classList.toggle('bg-secondary');
            });
        }
    </script>
</body>

</html>