<?php
include '../dbconnect/connect.php';

// Get all room types
$getRoomTypes = "SELECT * FROM roomtypes ORDER BY roomTypeID";
$roomTypesResult = executeQuery($getRoomTypes);

// Function to get features for a room
function getRoomFeatures($roomID) {
    $query = "SELECT f.featureName FROM features f 
              INNER JOIN roomfeatures rf ON f.featureId = rf.featureID 
              WHERE rf.roomID = " . (int)$roomID;
    return executeQuery($query);
}
?>
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
    <link rel="stylesheet" href="/HOTEL-MANAGEMENT-SYSTEM/css/style.css">
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
            <div class="col-12 col-lg-3 col-xl-2 px-0">
                <div class="sticky-top" style="top:70px; z-index: 10;">
                    <div class="bg-body-tertiary border p-4">
                        <div class="d-lg-none">
                            <div class="accordion" id="filterAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                                            Filter Rooms
                                        </button>
                                    </h2>
                                    <div id="collapseFilter" class="accordion-collapse collapse">
                                        <div class="accordion-body">
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
                                                <input type="range" class="form-range" min="1000" max="6000" step="100" id="priceRange" value="3500">
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-none d-lg-block">
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
                                <input type="range" class="form-range" min="1000" max="6000" step="100" id="priceRange" value="3500">
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
                    </div>
                </div>
            </div>

            <!-- Room Listings -->
            <div class="col-12 col-lg-9 col-xl-10 p-4">
                <h2 class="text-center fw-bold mb-4 fst-italic mt-5">Recommended Rooms</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;"></div>

                <?php 
                // Loop through each room type
                while ($roomType = mysqli_fetch_assoc($roomTypesResult)) { 
                    // Get rooms for this room type
                    $getRooms = "SELECT rooms.*, roomtypes.roomType AS roomTypeName 
                                 FROM rooms 
                                 INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID 
                                 WHERE rooms.roomTypeId = " . (int)$roomType['roomTypeID'];
                    $roomsResult = executeQuery($getRooms);
                    
                    // Only show section if there are rooms of this type
                    if (mysqli_num_rows($roomsResult) > 0) {
                ?>
                <div class="container">
                    <div class="row mt-5">
                        <div class="col">
                            <h2 class="fw-bold mb-3">
                                <?php echo htmlspecialchars($roomType['roomType']); ?> Room
                            </h2>
                        </div>
                    </div>
                    <div class="row" id="<?php echo strtolower($roomType['roomType']); ?>RoomCards">
                        <?php while($row = mysqli_fetch_assoc($roomsResult)) { 
                            // Get features for this room
                            $featuresResult = getRoomFeatures($row['roomID']);
                            $features = [];
                            while ($feature = mysqli_fetch_assoc($featuresResult)) {
                                $features[] = $feature['featureName'];
                            }
                        ?>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                            <div class="card h-100 bg-transparent shadow rounded-3">
                                <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                                    <img src="/HOTEL-MANAGEMENT-SYSTEM/admin/assets/<?php echo htmlspecialchars($row['imagePath']); ?>"
                                        class="card-img-top img-fluid" alt="<?php echo htmlspecialchars($row['roomName']); ?>">
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($row['roomName']); ?></h5>
                                    <p class="text-secondary fst-italic small mb-2"><?php echo htmlspecialchars($row['roomTypeName']); ?> Room • Max <?php echo (int)$row['capacity']; ?> Guests</p>
                                    <p class="fw-semibold mb-3">₱<?php echo number_format($row['base_price'], 2); ?> / night</p>
                                    <div class="mb-3">
                                        <?php if (!empty($features)) { 
                                            foreach ($features as $featureName) { ?>
                                                <span class="badge bg-dark me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                            <?php }
                                        } else { ?>
                                            <span class="text-muted small">No features listed</span>
                                        <?php } ?>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#bookingModal<?php echo $row['roomID']; ?>">Book Now</button>
                                        <button class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#roomDetailModal<?php echo $row['roomID']; ?>">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Room Detail Modal for Room <?php echo $row['roomID']; ?> -->
                        <div class="modal fade" id="roomDetailModal<?php echo $row['roomID']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?php echo htmlspecialchars($row['roomName']); ?></h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 justify-content-center text-center">
                                                <img src="/HOTEL-MANAGEMENT-SYSTEM/admin/assets/<?php echo htmlspecialchars($row['imagePath']); ?>" alt="<?php echo htmlspecialchars($row['roomName']); ?>"
                                                    class="img-fluid rounded-3 mb-3">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="fw-semibold mb-2">Room Details</p>
                                                <p class="small text-secondary mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($row['roomTypeName']); ?></p>
                                                <p class="small text-secondary mb-1"><strong>Capacity:</strong> <?php echo (int)$row['capacity']; ?> Guests</p>
                                                <p class="small text-secondary mb-1"><strong>Available:</strong> <?php echo (int)$row['quantity']; ?> Rooms</p>
                                                <p class="small text-secondary mb-1"><strong>Price:</strong> ₱<?php echo number_format($row['base_price'], 2); ?> / night</p>
                                            </div>
                                            <div class="col-4 align-items-center d-flex">
                                                <div class="mb-2 justify-content-evenly">
                                                    <?php if (!empty($features)) { 
                                                        foreach ($features as $featureName) { ?>
                                                            <span class="badge bg-dark me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                                        <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bookingModal<?php echo $row['roomID']; ?>">Book Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Modal for Room <?php echo $row['roomID']; ?> -->
                        <div class="modal fade" id="bookingModal<?php echo $row['roomID']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?php echo htmlspecialchars($row['roomName']); ?></h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 col-md-7 justify-content-center text-center">
                                                <img src="/HOTEL-MANAGEMENT-SYSTEM/admin/assets/<?php echo htmlspecialchars($row['imagePath']); ?>" alt="<?php echo htmlspecialchars($row['roomName']); ?>"
                                                    class="img-fluid rounded-3 mb-3">
                                                <div class="col-12 text-start mx-3">
                                                    <p class="fw-semibold mb-2">Features:</p>
                                                    <div class="mb-3">
                                                        <?php if (!empty($features)) { 
                                                            foreach ($features as $featureName) { ?>
                                                                <span class="badge bg-dark me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                                            <?php }
                                                        } ?>
                                                    </div>
                                                    <p class="small text-secondary"><strong>Type:</strong> <?php echo htmlspecialchars($row['roomTypeName']); ?> | <strong>Capacity:</strong> <?php echo (int)$row['capacity']; ?> Guests | <strong>Price:</strong> ₱<?php echo number_format($row['base_price'], 2); ?> / night</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-5">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p class="text-start fw-bold mb-1">Guest information</p>
                                                        <label for="fullName<?php echo $row['roomID']; ?>" class="form-label mb-0">Full Name</label>
                                                        <input type="text" id="fullName<?php echo $row['roomID']; ?>" class="form-control mb-1">
                                                        <label for="email<?php echo $row['roomID']; ?>" class="form-label mb-0">Email Address</label>
                                                        <input type="email" id="email<?php echo $row['roomID']; ?>" class="form-control mb-1">
                                                        <label for="phoneNumber<?php echo $row['roomID']; ?>" class="form-label mb-0">Phone Number</label>
                                                        <input type="tel" id="phoneNumber<?php echo $row['roomID']; ?>" class="form-control mb-1">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <p class="text-start fw-bold mt-1 mb-1">Booking details</p>
                                                    <div class="col-5">
                                                        <label for="checkIn<?php echo $row['roomID']; ?>" class="form-label mb-0">Check-in</label>
                                                        <input type="date" id="checkIn<?php echo $row['roomID']; ?>" class="form-control mb-1">
                                                    </div>
                                                    <div class="col-5">
                                                        <label for="checkOut<?php echo $row['roomID']; ?>" class="form-label mb-0">Check-out</label>
                                                        <input type="date" id="checkOut<?php echo $row['roomID']; ?>" class="form-control mb-1">
                                                    </div>
                                                    <div class="col-2">
                                                        <label for="guests<?php echo $row['roomID']; ?>" class="form-label mb-0">Guests</label>
                                                        <input type="number" id="guests<?php echo $row['roomID']; ?>" class="form-control mb-1" min="1" max="<?php echo (int)$row['capacity']; ?>" value="1">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 m-2">
                                                        <p class="text-start text-sm text-secondary fw-bold m-1">Booking Summary</p>
                                                        <div class="row">
                                                            <div class="col-6 ms-2 text-start">
                                                                <p class="mb-1"><strong>Room:</strong> <?php echo htmlspecialchars($row['roomName']); ?></p>
                                                                <p class="mb-1"><strong>Dates:</strong> <span id="summaryDates<?php echo $row['roomID']; ?>">-</span></p>
                                                                <p class="mb-1"><strong>Duration:</strong> <span id="summaryNights<?php echo $row['roomID']; ?>">-</span></p>
                                                                <p class="mb-1"><strong>Guests:</strong> <span id="summaryGuests<?php echo $row['roomID']; ?>">-</span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button class="btn btn-warning">Book Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php 
                    }
                } 
                ?>
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

            document.querySelectorAll('.btn-outline-dark, .btn-outline-light').forEach(element => {
                element.classList.toggle('btn-outline-dark');
                element.classList.toggle('btn-outline-light');
            });
        }
    </script>
</body>

</html>