<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../connect.php";

$objCon = new connect();


// =========================
// Add Category
// =========================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' ) {
    header("Location: /courses-platform/auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');

    // Check name
    if ($name === '') {

        $error = "Category name is required";

    }

    // Check icon
    elseif (!isset($_FILES['icon']) || $_FILES['icon']['error'] != 0) {

        $error = "Please choose an icon";

    } else {

        // Icon name
        $iconName = time() . "_" . basename($_FILES['icon']['name']);

        // Upload path
        $uploadPath = "../upload/" . $iconName;

        // Upload icon
        if (
            move_uploaded_file(
                $_FILES['icon']['tmp_name'],
                $uploadPath
            )
        ) {

            // Data to database
            $data = [
                'name' => $name,
                'icon' => $iconName
            ];

            // Insert
            if ($objCon->insert($data, "categories")) {

                header("Location: /courses-platform/categories/add.php?add=success");
                exit();

            } else {

                $error = "Add category failed";
            }

        } else {

            $error = "Failed to upload icon";
        }
    }
}


// Header
include "../includes/header.php";


// Success message
if (isset($_GET['add']) && $_GET['add'] === 'success') {

    $objCon->alert(
        "Category added successfully",
        "success"
    );
}


// Error message
if (isset($error)) {

    $objCon->alert(
        $error,
        "danger"
    );
}

?>

<link rel="stylesheet" href="assets\cs\style.css">
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Category</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

</head>


<body class="bg-light">


    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-md-7 col-lg-6">


                <!-- Card -->
                <div class="card shadow border-0 rounded-4">


                    <!-- Header -->
                    <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">

                        <h2 class="fw-bold mb-1">
                            Add Category
                        </h2>

                        <p class="mb-0">
                            Create a new course category
                        </p>

                    </div>


                    <!-- Body -->
                    <div class="card-body p-4 p-md-5">


                        <form action="" method="POST" enctype="multipart/form-data">


                            <!-- Category Name -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Category Name
                                </label>

                                <input class="form-control form-control-lg" type="text" name="name"
                                    placeholder="Enter category name" required>

                            </div>


                            <!-- Category Icon -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Category Icon
                                </label>

                                <input class="form-control form-control-lg" type="file" name="icon" accept="image/*"
                                    required>

                                <div class="form-text">
                                    Choose an image for the category icon.
                                </div>

                            </div>


                            <!-- Buttons -->
                            <div class="d-grid gap-2 mt-4">

                                <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                    Add Category
                                </button>

                                <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-3">
                                    Back to Categories
                                </a>

                            </div>


                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script src="../assets/js/bootstrap.bundle.min.js"></script>

</body>

</html>


<?php

include "../includes/footer.php";

?>