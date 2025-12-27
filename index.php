<?php
include 'dbconnect/connect.php';
//   $getQuery = "SELECT * FROM students";
//   $result = executeQuery($getQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates</title>
    <link rel="icon" type="image/png" href="images/flag.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'integrations/chatbot/chatbotUI.html'; ?>
    <?php include 'frontend/includes/navbar.php'; ?>

    <div id="home" class="position-relative d-flex align-items-center justify-content-center"
        style="min-height: 95vh; background: url('images/loginRegisterImg/img.jpg') center/cover no-repeat fixed;">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50"></div>
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 text-black text-center">
                    <h1 class="fw-bold mb-3" style="color: white; text-shadow: 0 4px 15px rgba(0,0,0,0.9);">
                        Welcome to
                    </h1>
                    <h1 class="display-1 fw-bold mb-3" style="color: white; text-shadow: 0 4px 15px rgba(0,0,0,0.9);">
                        TravelMates Hotel
                    </h1>
                    <p class="lead fs-4 mb-5 opacity-75" style="color: white; text-shadow: 0 4px 15px rgba(0,0,0,0.9);">
                        travelmits // aayusin pa to
                    </p>
                    <a href="frontend/rooms.php" class="btn btn-warning btn-lg">Book Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="mt-5 pt-4 mb-2 text-center fw-bold h-font">OUR ROOMS</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;"></div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row" id="ourRoomsContainer">

        </div>
    </div>
    <div class="col-lg-12 text-center my-4">
        <a href="frontend/rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms
            >>></a>
    </div>

    <div class="container mb-5">
        <div class="row align-items-center py-2">
            <div class="col-12 text-black text-md-start">
                <h2 class="mt-5 pt-4 mb-2 text-center fw-bold h-font">GALLERY</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;"></div>
            </div>
        </div>

        <!--bento box-->
        <div class="container-fluid px-0">
            <div class="row g-3">
                <!--left large-->
                <div class="col-12 col-md-8">
                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden">
                        <img src="images/rooms/basic.jpeg" alt="Gallery 1" class="img-fluid object-fit-cover">
                    </div>
                </div>
                <!--right column with 2 stacked images-->
                <div class="col-12 col-md-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="ratio ratio-4x3 rounded-3 overflow-hidden">
                                <img src="images/rooms/basic.jpeg" alt="Gallery 2" class="img-fluid object-fit-cover">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="ratio ratio-4x3 rounded-3 overflow-hidden">
                                <img src="images/rooms/basic.jpeg" alt="Gallery 3" class="img-fluid object-fit-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--bottom row with 3 images-->
            <div class="row g-3 mt-0">
                <div class="col-12 col-md-4">
                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden">
                        <img src="images/rooms/basic.jpeg" alt="Gallery 4" class="img-fluid object-fit-cover">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden">
                        <img src="images/rooms/basic.jpeg" alt="Gallery 5" class="img-fluid object-fit-cover">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden">
                        <img src="images/rooms/basic.jpeg" alt="Gallery 6" class="img-fluid object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="eventsContainer">
        <div class="row">
            <div class="col">
                <h2 class="mt-5 pt-4 mb-2 text-center fw-bold h-font">EVENTS</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;"></div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col">
            <!-- API para sa events nina jana dito ilalagay -->
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="mt-5 pt-4 mb-2 text-center fw-bold h-font">CUSTOMER REVIEWS</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;"></div>
            </div>
        </div>
    </div>
<?php /* ?> <!--remove nalang yung line nato pag may database na-->

<div class="container mt-5">
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold h-font">
                        <?php echo $row['username'] ?>
                    </h5>
                    <p class="card-text">
                        <?php echo $row['userReview'] ?>
                    </p>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php */ ?> <!--remove nalang yung line nato pag may database na-->

    <div class="col-lg-12 text-center my-4">
        <button class="btn btn-dark shadow-none" data-bs-toggle="modal" data-bs-target="#addReviewModal">
            <i class="bi bi-pencil-square me-2"></i>Add Your Review
        </button>
    </div>

    <div class="container" id="about">
        <div class="row">
            <div class="col">
                <h2 class="mt-5 mb-2 text-center fw-bold h-font">ABOUT US</h2>
                <div class="mx-auto mt-3 mb-5" style="width: 80px; height: 4px; background-color: #FF9900;">
                </div>
            </div>
        </div>
    </div>

    <div id="about-section" class="container-fluid py-5"
        style="background: linear-gradient(rgba(245, 240, 230, 0.85), rgba(245, 240, 230, 0.85)), url('images/loginRegisterImg/img.jpg') center/cover no-repeat;">
        <div class="row justify-content-center g-4">
            <div class="col-12 col-lg-5">
                <div
                    class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start text-center text-sm-start">
                    <div class="rounded-circle overflow-hidden border border-3 border-secondary flex-shrink-0 mb-3 mb-sm-0"
                        style="width: 200px; height: 200px;">
                        <img src="images/rooms/basic.jpeg" alt="..." class="img-fluid object-fit-cover w-100 h-100"
                            style="filter: grayscale(100%);">
                    </div>
                    <div class="ms-sm-4">
                        <h5 class="fw-bold text-uppercase text-secondary mb-3" style="letter-spacing: 2px;">A
                            Little
                            About Us</h5>
                        <p class="text-muted mb-0">Morbi leo risus, porta ac consectetur ac, vesti bulum at
                            eros. Fusce
                            dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum
                            massa justo
                            sit amet risus. Lorem ipsum dolor sit amet, consectetur.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div
                    class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start text-center text-sm-start">
                    <div class="rounded-circle overflow-hidden border border-3 border-secondary flex-shrink-0 mb-3 mb-sm-0"
                        style="width: 200px; height: 200px;">
                        <img src="images/loginRegisterImg/img.jpg" alt="..."
                            class="img-fluid object-fit-cover w-100 h-100" style="filter: grayscale(100%);">
                    </div>
                    <div class="ms-sm-4">
                        <h5 class="fw-bold text-uppercase text-secondary mb-3" style="letter-spacing: 2px;">Our
                            Collaborator</h5>
                        <p class="text-muted mb-0">Morbi leo risus, porta ac consectetur ac, vesti bulum at
                            eros. Fusce
                            dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum
                            massa justo
                            sit amet risus. Lorem ipsum dolor sit amet, consectetur.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row" id="membersContainer">

        </div>
    </div>

    <?php include 'frontend/includes/footer.php'; ?>

    <script src="js/ourRooms.js"></script>
    <script src="js/members.js"></script>
    <script>

        var ourRoomsContainer = document.getElementById("ourRoomsContainer");
        for (var i = 0; i < ourRooms.length; i++) {
            ourRoomsContainer.innerHTML += `
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-4">
                <div class="card h-100 bg-transparent shadow rounded-3">
                    <div class="ratio ratio-4x3 overflow-hidden rounded-top-3">
                        <img src="images/rooms/`+ ourRooms[i].images + `" alt="..." class="img-fluid">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">` + ourRooms[i].name + `</h5>
                        <p class="card-text">` + ourRooms[i].description + `</p>
                    </div>
                </div>
            </div>
      `;
        }

        var memberContainer = document.getElementById("membersContainer");
        for (var i = 0; i < members.length; i++) {
            memberContainer.innerHTML += `
            <div class="col-12 col-sm-6 col-md-4 col-lg pb-4 text-center">
                <div class="card h-100 bg-transparent border-0 rounded-3 align-items-center">
                    <div class="rounded-circle overflow-hidden border border-3 border-secondary flex-shrink-0 mb-3 mb-sm-0"
                        style="width: 200px; height: 200px;">
                        <img src="images/members-img/`+ members[i].images + `" alt="..."
                            class="img-fluid object-fit-cover w-100 h-100">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">` + members[i].name + `</h5>
                    </div>
                </div>
            </div>
      `;
        }

        function changeMode() {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
            document.querySelectorAll('#mode i, #mode-lg i').forEach(icon => {
                icon.className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
            });

            document.querySelectorAll('.text-black, .text-white').forEach(element => {
                element.classList.toggle('text-black');
                element.classList.toggle('text-white');
            });

            document.querySelectorAll('.btn-outline-dark, .btn-outline-light').forEach(element => {
                element.classList.toggle('btn-outline-dark');
                element.classList.toggle('btn-outline-light');
            });

            const aboutSection = document.querySelector('#about-section');
            if (aboutSection) {
                if (isDark) {
                    aboutSection.style.background = "linear-gradient(rgba(245, 240, 230, 0.85), rgba(245, 240, 230, 0.85)), url('images/loginRegisterImg/img.jpg') center/cover no-repeat";
                } else {
                    aboutSection.style.background = "linear-gradient(rgba(30, 30, 30, 0.9), rgba(30, 30, 30, 0.9)), url('images/loginRegisterImg/img.jpg') center/cover no-repeat";
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>