<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../connect.php";

$objCon = new connect();






 if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student'  ) {
    header("Location: /courses-platform/auth/login.php");
    exit;
}
// =========================
// Get Categories
// =========================

$categories = $objCon->select("categories");


// =========================
// Add Course
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $raw_date = $_POST['created_at'] ?? '';

    // تحويل صيغة التاريخ لـ YYYY-MM-DD HH:MM:SS المناسبة للـ MySQL
    $created_at = !empty($raw_date) ? date('Y-m-d H:i:s', strtotime($raw_date)) : date('Y-m-d H:i:s');


    // =========================
    // Check Title
    // =========================

    if ($title === '') {

        $error = "Course title is required";

    }


    // =========================
    // Check Description
    // =========================
    elseif ($description === '') {

        $error = "Course description is required";

    }


    // =========================
    // Check Price
    // =========================
    elseif ($price === '' || !is_numeric($price)) {

        $error = "Please enter a valid price";

    }


    // =========================
    // Check Category
    // =========================
    elseif ($category_id <= 0) {

        $error = "Please choose a category";

    }


    // =========================
    // Check Created At
    // =========================
    elseif ($raw_date === '') {

        $error = "Please choose created date";

    }


    // =========================
    // Check Image
    // =========================
    elseif (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] != 0
    ) {

        $error = "Please choose a course image";

    } else {

        // =========================
        // Upload Image
        // =========================

        $imageName =
            time() . "_" .
            basename($_FILES['image']['name']);

        $uploadPath = "../upload/" . $imageName;


        if (
            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $uploadPath
            )
        ) {

            // =========================
            // Course Data
            // =========================

            $data = [

                'title' => $title,

                'description' => $description,

                'image' => $imageName,

                'price' => $price,

                'teacher_id' => $_SESSION['user_id'],

                'category_id' => $category_id,

                'created_at' => $created_at

            ];


            // =========================
            // Insert Course
            // =========================

            if ($objCon->insert($data, "courses")) {

                // Return to dashboard
                header(
                    "Location: /courses-platform/teacher/dashboard.php?category_id=$category_id&add=success"
                );

                exit();

            } else {

                // إظهار الخطأ الحقيقي من الـ MySQL لو الاتصال متاح في الكلاس

                $error = "Add course failed! " . $objCon->getError();
            }


        } else {

            $error = "Failed to upload course image";

        }

    }

}


// =========================
// Header
// =========================

include "../includes/header.php";


// =========================
// Success Message
// =========================

if (
    isset($_GET['add']) &&
    $_GET['add'] === 'success'
) {

    $objCon->alert(
        "Course added successfully",
        "success"
    );
}


// =========================
// Error Message
// =========================

if (isset($error)) {

    $objCon->alert(
        $error,
        "danger"
    );
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Course</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

</head>


<body class="bg-light">


    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-md-8 col-lg-7">


                <!-- Card -->

                <div class="card shadow border-0 rounded-4">


                    <!-- Header -->

                    <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">

                        <h2 class="fw-bold mb-1">
                            Add Course
                        </h2>

                        <p class="mb-0">
                            Create a new course
                        </p>

                    </div>


                    <!-- Body -->

                    <div class="card-body p-4 p-md-5">


                        <form action="" method="POST" enctype="multipart/form-data">


                            <!-- Course Title -->

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Course Title
                                </label>

                                <input class="form-control form-control-lg" type="text" name="title"
                                    placeholder="Enter course title" required>

                            </div>


                            <!-- Description -->

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Description
                                </label>

                                <textarea class="form-control" name="description" rows="5"
                                    placeholder="Enter course description" required></textarea>

                            </div>


                            <!-- Course Image -->

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Course Image
                                </label>

                                <input class="form-control form-control-lg" type="file" name="image" accept="image/*"
                                    required>

                                <div class="form-text">
                                    Choose an image for the course.
                                </div>

                            </div>


                            <!-- Price -->

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Price
                                </label>

                                <input class="form-control form-control-lg" type="number" name="price" min="0"
                                    step="0.01" placeholder="Enter course price" required>

                            </div>


                            <!-- Category -->

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Category
                                </label>

                                <select class="form-select form-select-lg" name="category_id" required>

                                    <option value="">
                                        Choose Category
                                    </option>

                                    <?php foreach ($categories as $category) { ?>

                                        <option value="<?= $category['id'] ?>">
                                            <?= htmlspecialchars(
                                                $category['name']
                                            ) ?>
                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- Created At -->

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Created At
                                </label>

                                <input class="form-control form-control-lg" type="datetime-local" name="created_at"
                                    required>

                            </div>


                            <!-- Buttons -->

                            <div class="d-grid gap-2 mt-4">

                                <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                    Add Course
                                </button>


                                <!-- Back to Courses -->

                                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg rounded-3">
                                    Back to Courses
                                </a>

                            </div>


                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script src="/courses-platform/assets/js/bootstrap.bundle.min.js"></script>

</body>

</html>


<?php

include "../includes/footer.php";

?>