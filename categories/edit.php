<?php

include "../connect.php";

$objcon = new connect();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' ) {
    header("Location: /courses-platform/auth/login.php");
    exit;
}





$category = [];


// =========================
// Get Category
// =========================

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $category = $objcon->selectone("categories", $id);
}


// لو الـ category مش موجودة
if (empty($category)) {

    header("Location: /courses-platform/categories/index.php");
    exit();
}


// =========================
// Update Category
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');

    if ($name === '') {

        $error = "Category name is required";

    } else {

        // البيانات الأساسية
        $data = [
            'name' => $name
        ];


        // لو المستخدم اختار Icon جديدة
        if (isset($_FILES['icon']) && $_FILES['icon']['error'] === 0) {

            $iconName = time() . "_" . basename($_FILES['icon']['name']);

            $uploadPath = "../upload/" . $iconName;


            if (
                move_uploaded_file(
                    $_FILES['icon']['tmp_name'],
                    $uploadPath
                )
            ) {

                $data['icon'] = $iconName;

            } else {

                $error = "Failed to upload icon";
            }
        }


        // Update
        if (!isset($error)) {

            if ($objcon->update($data, "categories", $id)) {

                header("Location: /courses-platform/categories/index.php?update=success");
                exit();

            } else {

                $error = "Edit category failed";
            }
        }
    }
}


include "../header.php";


// Success message
if (isset($_GET['update']) && $_GET['update'] === 'success') {

    $objcon->alert(
        "Category updated successfully",
        "success"
    );
}


// Error message
if (isset($error)) {

    $objcon->alert(
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

    <title>Edit Category</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

</head>


<body class="bg-light">


    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-md-7 col-lg-6">


                <!-- Card -->
                <div class="card shadow border-0 rounded-4">


                    <!-- Header -->
                    <div class="card-header bg-warning text-dark text-center py-4 rounded-top-4">

                        <h2 class="fw-bold mb-1">
                            Edit Category
                        </h2>

                        <p class="mb-0">
                            Update category information
                        </p>

                    </div>


                    <!-- Body -->
                    <div class="card-body p-4 p-md-5">


                        <form action="" method="POST" enctype="multipart/form-data">


                            <!-- ID -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Category ID
                                </label>

                                <input class="form-control form-control-lg bg-light" type="text"
                                    value="<?= htmlspecialchars($category['id']) ?>" disabled>

                            </div>


                            <!-- Name -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Category Name
                                </label>

                                <input class="form-control form-control-lg" type="text" name="name"
                                    value="<?= htmlspecialchars($category['name']) ?>" required>

                            </div>


                            <!-- Current Icon -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold d-block">
                                    Current Icon
                                </label>

                                <?php if (!empty($category['icon'])) { ?>

                                    <img src="/courses-platform/upload/<?= rawurlencode(basename($category['icon'])) ?>"
                                        alt="Category Icon" class="img-thumbnail mb-3"
                                        style="width: 120px; height: 120px; object-fit: cover;">

                                <?php } else { ?>

                                    <p class="text-secondary">
                                        No icon available
                                    </p>

                                <?php } ?>

                            </div>


                            <!-- New Icon -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    New Icon
                                </label>

                                <input class="form-control form-control-lg" type="file" name="icon" accept="image/*">

                                <div class="form-text">
                                    Leave empty to keep the current icon.
                                </div>

                            </div>


                            <!-- Buttons -->
                            <div class="d-grid gap-2 mt-4">

                                <button type="submit" class="btn btn-warning btn-lg rounded-3">
                                    Update Category
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
```