<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$movie_id = $_GET['id'];
$movie = getVideoById($movie_id);

if (!$movie) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (rentVideo($movie_id)) {
        $_SESSION['message'] = "You have successfully rented the movie.";
    } else {
        $error = "Unable to rent the movie.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent Movie</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: cover;
            background-position: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
            color: #fff !important;
        }
        .nav-link {
            color: #fff !important;
        }
        .confirmation-container {
            margin-top: 20px;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 20px;
            max-width: 400px;
        }
        .footer {
            background-color: rgba(0, 0, 0, 1);
            color: #fff;
            padding: 20px 0;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        .btn-primary, .btn-danger, .btn-warning, .btn-success {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="profile_customer.php">Video Rental</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="filter_movies.php">Search Video</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile_customer.php?page=view">View Videos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile_details.php?page=profile">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

    <div class="confirmation-container container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                <p class="card-text">Directed by: <?php echo $movie['director']; ?></p>
                <p class="card-text">Release Year: <?php echo $movie['release_year']; ?></p>
                <p class="card-text">Price: ₱<?php echo $movie['price']; ?></p><!-- Added price -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['message']; ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php else: ?>
                    <form method="post">
                        <button type="submit" class="btn btn-success">Confirm Rent</button>
                        <a href="profile_customer.php" class="btn btn-primary">Back to Home</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2024 Video Rental. All rights reserved.</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
