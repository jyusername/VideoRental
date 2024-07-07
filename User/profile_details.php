<?php
session_start();
require_once '../functions.php'; // Include your functions for database access

// Check if user is logged in as customer
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Get user details from session
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh; /* Ensure full viewport height */
            display: flex;
            flex-direction: column;
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

        .container {
            flex: 1; /* Grow to fill remaining vertical space */
            margin-top: 20px;
            margin-bottom: 20px; /* Add margin to separate content from footer */
        }

        .card {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            width: 100%; /* Ensure cards take full width */
            max-width: 600px; /* Adjust max-width as needed */
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .footer {
            background-color: rgba(0, 0, 0, 1);
            color: #fff;
            padding: 20px 0;
            text-align: center;
            width: 100%;
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

    <div class="container">
        <div class="card mx-auto">
            <div class="card-body">
                <h3 class="card-title">Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
                <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($user['postal_code']); ?></p>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>

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
