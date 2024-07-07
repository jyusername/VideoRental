<?php
session_start();
require '../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $director = $_POST['director'];
    $release_year = $_POST['release_year'];
    $price = $_POST['price'];
    $genre = $_POST['genre'];
    $format = $_POST['format'];
    $copies = $_POST['copies'];

    // Fetch the existing video details to get the current image path
    $existing_video = getVideoById($id);
    $image = $existing_video['image']; // Default to the current image

    // Handle file upload if a new image is selected
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $image = $target_file; // Update $image only if a new image is uploaded
        } else {
            $error_message = 'File is not an image.';
        }
    }

    // Update video details
    $success = editVideo($id, $title, $director, $release_year, $image, $price, $genre, $format, $copies);
    if ($success) {
        $success_message = 'Video updated successfully.';
        updateSessionVideos(); // Update session with the latest data
    } else {
        $error_message = 'Failed to update video.';
    }
}

// Fetch video details for editing
if (isset($_GET['id'])) {
    $video_id = $_GET['id'];
    $video = getVideoById($video_id);
    if (!$video) {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Video</title>
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

        .edit-container {
            margin-top: 20px;
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .card {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 20px;
            width: 80%;
            /* Adjust the width of the card */
            max-width: 800px;
            /* Max width to ensure it doesn't get too large */
        }

        .footer {
            background-color: rgba(0, 0, 0, 1);
            color: #fff;
            padding: 20px 0;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">Video Rental</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=view">View Videos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add.php">Add Video</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="edit-container container">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Edit Video</h3>
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <form action="edit.php?id=<?php echo $video_id; ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="<?php echo htmlspecialchars($video['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="director">Director</label>
                                <input type="text" name="director" class="form-control"
                                    value="<?php echo htmlspecialchars($video['director']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="release_year">Release Year</label>
                                <input type="number" name="release_year" class="form-control"
                                    value="<?php echo $video['release_year']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Upload New Image</label>
                                <input type="file" name="image" class="form-control-file" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" class="form-control"
                                    value="<?php echo $video['price']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="genre">Genre</label>
                                <select class="form-control" name="genre" required>
                                    <option value="drama" <?php echo ($video['genre'] === 'drama') ? 'selected' : ''; ?>>
                                        Drama</option>
                                    <option value="action" <?php echo ($video['genre'] === 'action') ? 'selected' : ''; ?>>
                                        Action</option>
                                    <option value="fantasy" <?php echo ($video['genre'] === 'fantasy') ? 'selected' : ''; ?>>
                                        Fantasy</option>
                                    <option value="horror" <?php echo ($video['genre'] === 'horror') ? 'selected' : ''; ?>>
                                        Horror</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="format">Format</label>
                                <select class="form-control" name="format" required>
                                    <option value="DVD" <?php echo ($video['format'] === 'DVD') ? 'selected' : ''; ?>>
                                        DVD</option>
                                    <option value="Blu-ray"
                                        <?php echo ($video['format'] === 'Blu-ray') ? 'selected' : ''; ?>>Blu-ray
                                    </option>
                                    <option value="Digital"
                                        <?php echo ($video['format'] === 'Digital') ? 'selected' : ''; ?>>Digital
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="copies">Number of Copies Available</label>
                                <input type="number" name="copies" class="form-control"
                                    value="<?php echo $video['copies']; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Video</button>
                        </div>
                    </div>
                </form>
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
