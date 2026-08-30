<?php

include "../header.php";
include "../connect.php";

$objcon = new connect();

$categories = $objcon->select("categories");

?>

<div class="container py-5">

    <!-- العنوان -->
    <div class="mb-5">

        <small class="text-primary fw-bold">
            CATEGORIES
        </small>

        <h1 class="fw-bold mt-3">
            Popular Categories
        </h1>

        <p class="text-secondary fs-5">
            Pick a track and start building real skills today.
        </p>

        <a href="index.php" class="text-primary fw-bold text-decoration-none">
            All categories →
        </a>

    </div>


    <!-- Categories -->
    <div class="row">

        <?php foreach ($categories as $category) { ?>

            <div class="col-12 mb-4">

                <div class="category-card">

                    <!-- Icon -->
                    <div class="category-icon">

                        <?php if (!empty($category['icon'])) { ?>

                            <img src="/courses-platform/upload/<?= rawurlencode(basename($category['icon'])) ?>"
                                alt="<?= htmlspecialchars($category['name']) ?> icon">

                        <?php } else { ?>

                            <i class="bi bi-code-slash"></i>

                        <?php } ?>

                    </div>


                    <!-- Content -->
                    <div class="category-content">

                        <h4>
                            <?= htmlspecialchars($category['name']) ?>
                        </h4>




                    </div>


                    <!-- Admin buttons -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>

                        <div class="category-actions">

                            <a href="edit.php?id=<?= $category['id'] ?>" class="btn btn-warning">
                                <i class="bi bi-pencil"></i>
                                تعديل
                            </a>

                            <a href="delete.php?id=<?= $category['id'] ?>" class="btn btn-danger"
                                onclick="return confirm('هل أنت متأكد من حذف التصنيف؟')">
                                <i class="bi bi-trash"></i>
                                حذف
                            </a>

                        </div>

                    <?php } ?>

                </div>

            </div>

        <?php } ?>

    </div>

</div>