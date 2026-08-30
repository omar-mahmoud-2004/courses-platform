
<?php

include "../connect.php";

$objCon = new connect();

 if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student'  ) {
    header("Location: /courses-platform/auth/login.php");
    exit;
}
// =========================
// Get Course
// =========================

$course = [];

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $course = $objCon->selectone("courses", $id);
}


// =========================
// Check Course
// =========================

// if (empty($course)) {

//     header("Location: /courses-platform/courses/index.php");
//     exit();

// }


// =========================
// Get Categories
// =========================

$categories = $objCon->select("categories");


// =========================
// Update Course
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $created_at = $_POST['created_at'] ?? '';


    // =========================
    // Teacher ID
    // =========================

    // TODO:
    // عندما يتم إنشاء teachers/users
    // يتم استقبال teacher_id من الفورم.
    //
    // مثال:
    // $teacher_id = (int) ($_POST['teacher_id'] ?? 0);


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

    elseif ($created_at === '') {

        $error = "Please choose created date";

    }


    else {


        // =========================
        // Course Data
        // =========================

        $data = [

            'title' => $title,

            'description' => $description,

            'price' => $price,

            'category_id' => $category_id,

            'created_at' => $created_at

        ];


        // =========================
        // New Image
        // =========================

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === 0
        ) {

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

                $data['image'] = $imageName;

            } else {

                $error = "Failed to upload new image";
            }
        }


        // =========================
        // Update Course
        // =========================

        if (!isset($error)) {

            if (
                $objCon->update(
                    $data,
                    "courses",
                    $id
                )
            ) {

                header(
                    "Location: /courses-platform/teacher/dashboard.php?update=success"
                );

                exit();

            } else {

                $error = "Edit course failed";
            }
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
    isset($_GET['update']) &&
    $_GET['update'] === 'success'
) {

    $objCon->alert(
        "Course updated successfully",
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Course</title>

    <link
        rel="stylesheet"
        href="../assets/css/bootstrap.min.css"
    >

</head>


<body class="bg-light">


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-7">


            <!-- Card -->

            <div class="card shadow border-0 rounded-4">


                <!-- Header -->

                <div
                    class="card-header bg-warning text-dark text-center py-4 rounded-top-4"
                >

                    <h2 class="fw-bold mb-1">
                        Edit Course
                    </h2>

                    <p class="mb-0">
                        Update course information
                    </p>

                </div>


                <!-- Body -->

                <div class="card-body p-4 p-md-5">


                    <form
                        action=""
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <!-- Course Title -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Course Title
                            </label>

                            <input
                                class="form-control form-control-lg"
                                type="text"
                                name="title"
                                value="<?= htmlspecialchars($course['title']) ?>"
                                required
                            >

                        </div>


                        <!-- Description -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                name="description"
                                rows="5"
                                required
                            ><?= htmlspecialchars($course['description']) ?></textarea>

                        </div>


                        <!-- Current Image -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold d-block">
                                Current Image
                            </label>

                            <?php if (!empty($course['image'])) { ?>

                                <img
                                    src="/courses-platform/upload/<?= rawurlencode(basename($course['image'])) ?>"
                                    alt="<?= htmlspecialchars($course['title']) ?>"
                                    class="img-thumbnail mb-3"
                                    style="width: 160px; height: 100px; object-fit: cover;"
                                >

                            <?php } else { ?>

                                <p class="text-secondary">
                                    No image available
                                </p>

                            <?php } ?>

                        </div>


                        <!-- New Image -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                New Image
                            </label>

                            <input
                                class="form-control form-control-lg"
                                type="file"
                                name="image"
                                accept="image/*"
                            >

                            <div class="form-text">
                                Leave empty to keep the current image.
                            </div>

                        </div>


                        <!-- Price -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Price
                            </label>

                            <input
                                class="form-control form-control-lg"
                                type="number"
                                name="price"
                                min="0"
                                step="0.01"
                                value="<?= htmlspecialchars($course['price']) ?>"
                                required
                            >

                        </div>


                        <!-- Category -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Category
                            </label>

                            <select
                                class="form-select form-select-lg"
                                name="category_id"
                                required
                            >

                                <option value="">
                                    Choose Category
                                </option>

                                <?php foreach ($categories as $category) { ?>

                                    <option
                                        value="<?= $category['id'] ?>"
                                        <?= ($course['category_id'] == $category['id']) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $category['name']
                                        ) ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- Teacher ID -->

                        <!--
                        TODO:
                        عندما يتم إنشاء teachers/users
                        يتم تفعيل هذا الجزء.

                        ويتم إرسال teacher_id
                        إلى جدول courses.

                        مثال:

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Teacher
                            </label>

                            <select
                                class="form-select form-select-lg"
                                name="teacher_id"
                            >

                                <option value="">
                                    Choose Teacher
                                </option>

                                هنا يتم عرض أسماء المدرسين
                                والقيمة تكون ID المدرس.

                            </select>

                        </div>
                        -->


                        <!-- Created At -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Created At
                            </label>

                            <input
                                class="form-control form-control-lg"
                                type="datetime-local"
                                name="created_at"
                                value="<?= date('Y-m-d\TH:i', strtotime($course['created_at'])) ?>"
                                required
                            >

                        </div>


                        <!-- Buttons -->

                        <div class="d-grid gap-2 mt-4">

                            <button
                                type="submit"
                                class="btn btn-warning btn-lg rounded-3"
                            >
                                Update Course
                            </button>


                            <a
                                href="index.php"
                                class="btn btn-outline-secondary btn-lg rounded-3"
                            >
                                Back to Courses
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

<?php

// include "../connect.php";

// $objCon = new connect();

// if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
//     header("Location: /courses-platform/courses/index.php");
//     exit();
// }

// $id = (int) $_GET['id'];

// $course = $objCon->selectone("courses", $id);

// if (empty($course)) {
//     header("Location: /courses-platform/courses/index.php");
//     exit();
// }

// $categories = $objCon->select("categories");
?>