<?php
include '../dbconnect/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userData = null;
if (isset($_SESSION['userID'])) {
    $userID = (int) $_SESSION['userID'];
    $getUserQuery = "SELECT firstName, lastName, email, phoneNumber FROM users WHERE userID = $userID";
    $userResult = executeQuery($getUserQuery);
    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $userData = mysqli_fetch_assoc($userResult);
        $userData['fullName'] = $userData['firstName'] . ' ' . $userData['lastName'];
    }
}

$getRoomTypes = "SELECT * FROM roomtypes ORDER BY roomTypeID";
$roomTypesResult = executeQuery($getRoomTypes);

function getRoomFeatures($roomID)
{
    $query = "SELECT f.featureName FROM features f INNER JOIN roomfeatures rf ON f.featureId = rf.featureID WHERE rf.roomID = " . (int) $roomID;
    return executeQuery($query);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - Rooms</title>
    <link rel="icon" type="image/png" href="../images/logo/logoW.png">
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
    <?php include '../integrations/chatbot/chatbotUI.php'; ?>
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
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFilter"
                                            aria-expanded="false" aria-controls="collapseFilter">
                                            Filter Rooms
                                        </button>
                                    </h2>
                                    <div id="collapseFilter" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <!-- Room Type -->
                                            <div class="border-bottom pb-3 mb-3">
                                                <h6 class="fw-semibold mb-3 text-secondary">Room Type</h6>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="basic"
                                                        id="typeBasic">
                                                    <label class="form-check-label small" for="typeBasic">Basic</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="twin"
                                                        id="typeTwin">
                                                    <label class="form-check-label small" for="typeTwin">Twin</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="deluxe"
                                                        id="typeDeluxe">
                                                    <label class="form-check-label small"
                                                        for="typeDeluxe">Deluxe</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="single"
                                                        id="typeSingle">
                                                    <label class="form-check-label small"
                                                        for="typeSingle">Single</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="family"
                                                        id="typeFamily">
                                                    <label class="form-check-label small"
                                                        for="typeFamily">Family</label>
                                                </div>
                                            </div>

                                            <!-- Price Range -->
                                            <div class="border-bottom pb-3 mb-3">
                                                <h6 class="fw-semibold mb-3 text-secondary">Price Range (₱)</h6>
                                                <input type="range" class="form-range" min="1000" max="6000" step="100"
                                                    id="priceRange" value="3500">
                                                <div class="d-flex justify-content-between small text-muted">
                                                    <span>₱1,000</span>
                                                    <span>₱6,000</span>
                                                </div>
                                            </div>

                                            <!-- Facilities -->
                                            <div class="border-bottom pb-3 mb-3">
                                                <h6 class="fw-semibold mb-3 text-secondary">Facilities</h6>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="wifi"
                                                        id="facilityWifi">
                                                    <label class="form-check-label small"
                                                        for="facilityWifi">WiFi</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="aircon"
                                                        id="facilityAircon">
                                                    <label class="form-check-label small"
                                                        for="facilityAircon">Air-conditioner</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="tv"
                                                        id="facilityTv">
                                                    <label class="form-check-label small"
                                                        for="facilityTv">Television</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="kitchen"
                                                        id="facilityKitchen">
                                                    <label class="form-check-label small"
                                                        for="facilityKitchen">Kitchen</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="parking"
                                                        id="facilityParking">
                                                    <label class="form-check-label small"
                                                        for="facilityParking">Parking</label>
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
                                    <input class="form-check-input" type="checkbox" value="kitchen"
                                        id="facilityKitchen">
                                    <label class="form-check-label small" for="facilityKitchen">Kitchen</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="parking"
                                        id="facilityParking">
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
                while ($roomType = mysqli_fetch_assoc($roomTypesResult)) {
                    $getRooms = "SELECT rooms.*, roomtypes.roomType AS roomTypeName FROM rooms 
                                INNER JOIN roomtypes ON rooms.roomTypeId = roomtypes.roomTypeID 
                                WHERE rooms.roomTypeId = " . (int) $roomType['roomTypeID'];
                    $roomsResult = executeQuery($getRooms);
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
                                <?php while ($row = mysqli_fetch_assoc($roomsResult)) {
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
                                                    class="card-img-top img-fluid"
                                                    alt="<?php echo htmlspecialchars($row['roomName']); ?>">
                                            </div>
                                            <div class="card-body p-4">
                                                <h5 class="card-title fw-bold mb-1">
                                                    <?php echo htmlspecialchars($row['roomName']); ?>
                                                </h5>
                                                <p class="text-secondary fst-italic small mb-2">
                                                    <?php echo htmlspecialchars($row['roomTypeName']); ?> Room • Max
                                                    <?php echo (int) $row['capacity']; ?> Guests
                                                </p>
                                                <p class="fw-semibold mb-3">₱<?php echo number_format($row['base_price'], 2); ?> /
                                                    night</p>
                                                <div class="mb-3">
                                                    <?php if (!empty($features)) {
                                                        foreach ($features as $featureName) { ?>
                                                            <span
                                                                class="badge bg-dark text-white me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                                        <?php }
                                                    } else { ?>
                                                        <span class="text-muted small">No features listed</span>
                                                    <?php } ?>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#bookingModal<?php echo $row['roomID']; ?>">Book
                                                        Now</button>
                                                    <button class="btn btn-outline-secondary" data-bs-toggle="modal"
                                                        data-bs-target="#roomDetailModal<?php echo $row['roomID']; ?>">More
                                                        Details</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                                                            <img src="/HOTEL-MANAGEMENT-SYSTEM/admin/assets/<?php echo htmlspecialchars($row['imagePath']); ?>"
                                                                alt="<?php echo htmlspecialchars($row['roomName']); ?>"
                                                                class="img-fluid rounded-3 mb-3">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <p class="fw-semibold mb-2">Room Details</p>
                                                            <p class="small text-secondary mb-1"><strong>Type:</strong>
                                                                <?php echo htmlspecialchars($row['roomTypeName']); ?></p>
                                                            <p class="small text-secondary mb-1"><strong>Capacity:</strong>
                                                                <?php echo (int) $row['capacity']; ?> Guests</p>
                                                            <p class="small text-secondary mb-1"><strong>Available:</strong>
                                                                <?php echo (int) $row['quantity']; ?> Rooms</p>
                                                            <p class="small text-secondary mb-1"><strong>Price:</strong>
                                                                ₱<?php echo number_format($row['base_price'], 2); ?> / night</p>
                                                        </div>
                                                        <div class="col-4 align-items-center d-flex">
                                                            <div class="mb-2 justify-content-evenly">
                                                                <?php if (!empty($features)) {
                                                                    foreach ($features as $featureName) { ?>
                                                                        <span
                                                                            class="badge bg-dark me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                                                    <?php }
                                                                } ?>
                                                            </div>
                                                            <div class="mb-2 justify-content-evenly">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#bookingModal<?php echo $row['roomID']; ?>">Book
                                                        Now</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="bookingModal<?php echo $row['roomID']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><?php echo htmlspecialchars($row['roomName']); ?></h5>
                                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="bookingForm<?php echo $row['roomID']; ?>"
                                                        action="php/process_booking.php" method="POST">
                                                        <input type="hidden" name="roomID" value="<?php echo $row['roomID']; ?>">
                                                        <input type="hidden" name="totalPrice"
                                                            id="totalPriceInput<?php echo $row['roomID']; ?>"
                                                            value="<?php echo $row['base_price']; ?>">
                                                        <input type="hidden" name="paymentMethod"
                                                            id="paymentMethodInput<?php echo $row['roomID']; ?>" value="">

                                                        <div class="row">
                                                            <div class="col-12 col-md-7 justify-content-center text-center">
                                                                <img src="/HOTEL-MANAGEMENT-SYSTEM/admin/assets/<?php echo htmlspecialchars($row['imagePath']); ?>"
                                                                    alt="<?php echo htmlspecialchars($row['roomName']); ?>"
                                                                    class="img-fluid rounded-3 mb-3">
                                                                <div class="col-12 text-start mx-3">
                                                                    <p class="fw-semibold mb-2">Features:</p>
                                                                    <div class="mb-3">
                                                                        <?php if (!empty($features)) {
                                                                            foreach ($features as $featureName) { ?>
                                                                                <span
                                                                                    class="badge bg-dark me-1 mb-1"><?php echo htmlspecialchars($featureName); ?></span>
                                                                            <?php }
                                                                        } ?>
                                                                    </div>
                                                                    <p class="small text-secondary"><strong>Type:</strong>
                                                                        <?php echo htmlspecialchars($row['roomTypeName']); ?> |
                                                                        <strong>Capacity:</strong>
                                                                        <?php echo (int) $row['capacity']; ?> Guests |
                                                                        <strong>Price:</strong>
                                                                        ₱<?php echo number_format($row['base_price'], 2); ?> / night
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-5">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <p class="text-start fw-bold mb-1">Guest information</p>
                                                                        <?php if ($userData): ?>
                                                                            <div class="row g-2">
                                                                                <div class="col-12 col-sm-6">
                                                                                    <input type="text" name="firstName" id="firstName"
                                                                                        class="form-control mb-2"
                                                                                        placeholder="First name"
                                                                                        value="<?php echo htmlspecialchars($userData['firstName'] ?? '', ENT_QUOTES); ?>"
                                                                                        required>
                                                                                </div>
                                                                                <div class="col-12 col-sm-6">
                                                                                    <input type="text" name="lastName" id="lastName"
                                                                                        class="form-control mb-2"
                                                                                        placeholder="Last name"
                                                                                        value="<?php echo htmlspecialchars($userData['lastName'] ?? '', ENT_QUOTES); ?>"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <input type="email" name="email" class="form-control mb-2"
                                                                                placeholder="Email"
                                                                                value="<?php echo htmlspecialchars($userData['email'] ?? '', ENT_QUOTES); ?>"
                                                                                required>
                                                                            <input type="tel" name="phoneNumber"
                                                                                class="form-control mb-2" placeholder="Phone Number"
                                                                                value="<?php echo htmlspecialchars($userData['phoneNumber'] ?? '', ENT_QUOTES); ?>"
                                                                                required>
                                                                        <?php else: ?>
                                                                            <div class="alert alert-warning small">
                                                                                <i class="bi bi-info-circle me-1"></i>Please log in to
                                                                                book a room. Fields are disabled until you sign in.
                                                                            </div>
                                                                            <div class="row g-2">
                                                                                <div class="col-12 col-sm-6">
                                                                                    <input type="text" name="firstName" id="firstName" disabled
                                                                                        class="form-control mb-2"
                                                                                        placeholder="First name"
                                                                                        value="<?php echo htmlspecialchars($userData['firstName'] ?? '', ENT_QUOTES); ?>"
                                                                                        required>
                                                                                </div>
                                                                                <div class="col-12 col-sm-6">
                                                                                    <input type="text" name="lastName" id="lastName" disabled
                                                                                        class="form-control mb-2"
                                                                                        placeholder="Last name"
                                                                                        value="<?php echo htmlspecialchars($userData['lastName'] ?? '', ENT_QUOTES); ?>"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <input type="text" class="form-control mb-2"
                                                                                placeholder="Last name" disabled>
                                                                            <input type="email" class="form-control mb-2"
                                                                                placeholder="Email" disabled>
                                                                            <input type="tel" class="form-control mb-2"
                                                                                placeholder="Phone Number" disabled>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <p class="text-start fw-bold mt-1 mb-1">Booking details</p>
                                                                    <div class="col-5">
                                                                        <label for="checkIn<?php echo $row['roomID']; ?>"
                                                                            class="form-label mb-0">Check-in</label>
                                                                        <input type="date" name="checkInDate"
                                                                            id="checkIn<?php echo $row['roomID']; ?>"
                                                                            class="form-control mb-1" required>
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <label for="checkOut<?php echo $row['roomID']; ?>"
                                                                            class="form-label mb-0">Check-out</label>
                                                                        <input type="date" name="checkOutDate"
                                                                            id="checkOut<?php echo $row['roomID']; ?>"
                                                                            class="form-control mb-1" required>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <label for="guests<?php echo $row['roomID']; ?>"
                                                                            class="form-label mb-0">Guests</label>
                                                                        <input type="number" name="numberOfGuests"
                                                                            id="guests<?php echo $row['roomID']; ?>"
                                                                            class="form-control mb-1" min="1"
                                                                            max="<?php echo (int) $row['capacity']; ?>" value="1"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-12 m-2">
                                                                        <p class="text-start text-sm text-secondary fw-bold m-1">
                                                                            Booking Summary</p>
                                                                        <div class="row">
                                                                            <div class="col-12 ms-2 text-start">
                                                                                <p class="mb-1"><strong>Room:</strong>
                                                                                    <?php echo htmlspecialchars($row['roomName']); ?>
                                                                                </p>
                                                                                <p class="mb-1"><strong>Dates:</strong> <span
                                                                                        id="summaryDates<?php echo $row['roomID']; ?>">-</span>
                                                                                </p>
                                                                                <p class="mb-1"><strong>Duration:</strong> <span
                                                                                        id="summaryNights<?php echo $row['roomID']; ?>">-</span>
                                                                                </p>
                                                                                <p class="mb-1"><strong>Guests:</strong> <span
                                                                                        id="summaryGuests<?php echo $row['roomID']; ?>">1</span>
                                                                                </p>
                                                                                <p class="mb-1 fw-bold text-warning">
                                                                                    <strong>Total:</strong> ₱<span
                                                                                        id="summaryTotal<?php echo $row['roomID']; ?>"><?php echo number_format($row['base_price'], 2); ?></span>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-warning"
                                                        onclick="openPaymentModal(<?php echo $row['roomID']; ?>, <?php echo $row['base_price']; ?>)">Proceed
                                                        to Payment</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Modal -->
                                    <div class="modal fade" id="paymentModal<?php echo $row['roomID']; ?>" tabindex="-1"
                                        data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        onclick="closePaymentModal(<?php echo $row['roomID']; ?>)"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-4">
                                                        <h6 class="text-muted">Total Amount</h6>
                                                        <h2 class="text-warning fw-bold">₱<span
                                                                id="paymentTotal<?php echo $row['roomID']; ?>">0.00</span></h2>
                                                    </div>

                                                    <h6 class="fw-bold mb-3">Select Payment Method</h6>
                                                    <div class="d-grid gap-2">
                                                        <button type="button" class="btn btn-outline-secondary payment-method-btn"
                                                            data-method="paypal"
                                                            onclick="selectPayment(<?php echo $row['roomID']; ?>, 'paypal')">
                                                            <i class="bi bi-paypal me-2"></i>PayPal
                                                        </button>
                                                    </div>

                                                    <!-- Payment Details Section (hidden by default) -->
                                                    <div id="paymentDetails<?php echo $row['roomID']; ?>" class="mt-4"
                                                        style="display: none;">
                                                        <hr>
                                                        <!-- Only PayPal is supported: other payment detail sections removed -->
                                                        <div id="paypalDetails<?php echo $row['roomID']; ?>" class="payment-detail"
                                                            style="display: none;">
                                                            <div class="alert alert-info small">
                                                                <i class="bi bi-info-circle me-1"></i>You will be redirected to
                                                                PayPal to complete your secure online payment.
                                                            </div>
                                                        </div>

                                                        <button type="button" class="btn btn-warning w-100 mt-3"
                                                            onclick="confirmPayment(<?php echo $row['roomID']; ?>)">
                                                            <i class="bi bi-check-circle me-2"></i>Confirm Booking
                                                        </button>
                                                    </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            <?php
            mysqli_data_seek($roomTypesResult, 0);
            while ($roomType = mysqli_fetch_assoc($roomTypesResult)) {
                $getRooms = "SELECT * FROM rooms WHERE roomTypeId = " . (int) $roomType['roomTypeID'];
                $roomsResult = executeQuery($getRooms);
                while ($room = mysqli_fetch_assoc($roomsResult)) {
                    ?>
                    setupBookingCalculation(<?php echo $room['roomID']; ?>, <?php echo $room['base_price']; ?>);
                    <?php
                }
            }
            ?>
        });

        function setupBookingCalculation(roomID, basePrice) {
            const checkIn = document.getElementById('checkIn' + roomID);
            const checkOut = document.getElementById('checkOut' + roomID);
            const guests = document.getElementById('guests' + roomID);

            if (checkIn && checkOut && guests) {
                const today = new Date().toISOString().split('T')[0];
                checkIn.min = today;
                checkOut.min = today;

                checkIn.addEventListener('change', () => updateSummary(roomID, basePrice));
                checkOut.addEventListener('change', () => updateSummary(roomID, basePrice));
                guests.addEventListener('change', () => updateSummary(roomID, basePrice));
            }
        }

        function updateSummary(roomID, basePrice) {
            const checkIn = document.getElementById('checkIn' + roomID).value;
            const checkOut = document.getElementById('checkOut' + roomID).value;
            const guests = document.getElementById('guests' + roomID).value;

            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

                if (nights > 0) {
                    const total = basePrice * nights;
                    document.getElementById('summaryDates' + roomID).textContent =
                        checkInDate.toLocaleDateString() + ' - ' + checkOutDate.toLocaleDateString();
                    document.getElementById('summaryNights' + roomID).textContent = nights + ' night(s)';
                    document.getElementById('summaryGuests' + roomID).textContent = guests;
                    document.getElementById('summaryTotal' + roomID).textContent = total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
                    document.getElementById('totalPriceInput' + roomID).value = total;
                }
            }
        }

        let selectedPaymentMethod = {};

        function openPaymentModal(roomID, basePrice) {
            const form = document.getElementById('bookingForm' + roomID);
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const total = document.getElementById('totalPriceInput' + roomID).value;
            document.getElementById('paymentTotal' + roomID).textContent =
                parseFloat(total).toLocaleString('en-PH', { minimumFractionDigits: 2 });

            const bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal' + roomID));
            bookingModal.hide();

            setTimeout(() => {
                const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal' + roomID));
                paymentModal.show();
            }, 300);
        }

        function closePaymentModal(roomID) {
            selectedPaymentMethod[roomID] = null;
            // hide any payment detail sections
            document.querySelectorAll('#paymentModal' + roomID + ' .payment-detail').forEach(el => {
                el.style.display = 'none';
            });
            // hide the payment details container
            const paymentContainerHide = document.getElementById('paymentDetails' + roomID);
            if (paymentContainerHide) paymentContainerHide.style.display = 'none';
            // reset buttons
            document.querySelectorAll('#paymentModal' + roomID + ' .payment-method-btn').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
            });
        }

        function selectPayment(roomID, method) {
            selectedPaymentMethod[roomID] = method;
            const input = document.getElementById('paymentMethodInput' + roomID);
            if (input) input.value = method;

            // remove active state on all buttons
            document.querySelectorAll('#paymentModal' + roomID + ' .payment-method-btn').forEach(btn => btn.classList.remove('active'));

            // mark the chosen button active (we added data-method attribute on PayPal button)
            const chosenBtn = document.querySelector('#paymentModal' + roomID + ' .payment-method-btn[data-method="' + method + '"]');
            if (chosenBtn) chosenBtn.classList.add('active');

            // hide all detail panes then show the one for the chosen method
            document.querySelectorAll('#paymentModal' + roomID + ' .payment-detail').forEach(el => el.style.display = 'none');
            // show parent container and the specific detail pane
            const paymentContainer = document.getElementById('paymentDetails' + roomID);
            if (paymentContainer) paymentContainer.style.display = 'block';
            const detail = document.getElementById(method + 'Details' + roomID);
            if (detail) detail.style.display = 'block';
        }

        function confirmPayment(roomID) {
            if (!selectedPaymentMethod[roomID]) {
                alert('Please select a payment method');
                return;
            }

            const btn = (event && event.target) ? event.target : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }

            // If PayPal, submit booking via AJAX to create a pending booking, then redirect to PayPal approval
            if (selectedPaymentMethod[roomID] === 'paypal') {
                const total = document.getElementById('totalPriceInput' + roomID).value;
                const form = document.getElementById('bookingForm' + roomID);
                if (!form) {
                    alert('Booking form not found.');
                    return;
                }

                // Build FormData from the existing booking form and add ajax flag
                const fd = new FormData(form);
                fd.append('ajax', '1');

                // Ensure paymentMethod is paypal
                fd.set('paymentMethod', 'paypal');

                fetch('/HOTEL-MANAGEMENT-SYSTEM/frontend/php/process_booking.php', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    if (data && data.success && data.bookingID) {
                        // Redirect to create order with bookingID so capture can update paymentStatus
                        const url = '/HOTEL-MANAGEMENT-SYSTEM/integrations/paypal/create_order.php?roomID=' + encodeURIComponent(roomID) + '&amount=' + encodeURIComponent(total) + '&bookingID=' + encodeURIComponent(data.bookingID);
                        window.location.href = url;
                    } else {
                        alert('Failed to create booking before redirecting to PayPal.');
                        location.reload();
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Network error creating booking. Please try again.');
                });
                return;
            }

            // Default: submit booking form (other payment methods handled by server)
            setTimeout(() => {
                document.getElementById('bookingForm' + roomID).submit();
            }, 800);
        }
    </script>

</body>
</html>