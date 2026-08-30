<?php

include "../includes/header.php";
include "../connect.php";

$objcon = new connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================
// Delete Category (Admin Only)
// =========================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $id = (int) $_POST['id'];

        $status = $objcon->delete("categories", $id)
            ? "success"
            : "failed";

        header("Location: /courses-platform/categories/index.php?delete=$status");
        exit;
    }
}

// =========================
// Get Categories
// =========================

$categories = $objcon->select("categories");
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

?>

<style>
    .category-card {
        position: relative;
        cursor: pointer;
        min-height: 150px;
        padding: 25px;
        border-radius: 16px;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        transition: 0.25s ease;
    }

    .category-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.1);
    }

    .category-icon {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        overflow: hidden;
        background-color: #f8f9fa;
        margin-bottom: 15px;
    }

    .category-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .category-content h4 {
        margin: 0;
        font-weight: 700;
    }

    .category-actions {
        position: absolute;
        right: 18px;
        bottom: 18px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .category-actions .btn {
        font-size: 13px;
        padding: 5px 11px;
        border-radius: 6px;
    }

    .category-actions form {
        margin: 0;
    }
</style>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="container py-5">

    <!-- العنوان -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <small class="text-primary fw-bold text-uppercase">CATEGORIES</small>
            <h1 class="fw-bold mt-1">Popular Categories</h1>
            <p class="text-secondary fs-5 mb-0">Pick a track and start building real skills today.</p>
        </div>

        <?php if ($isAdmin): ?>
            <div>
                <a href="add.php" class="btn btn-success px-4 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success / Failed Message -->
    <?php if (isset($_GET['delete'])): ?>
        <?php if ($_GET['delete'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                Category deleted successfully
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php else: ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                Failed to delete category
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Categories Grid -->
    <div class="row g-4">

        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>

                <div class="col-12 col-md-6 col-lg-4">

                    <!-- Category Card -->
                    <div class="category-card h-100"
                        onclick="window.location.href='../courses/index.php?category_id=<?= $category['id'] ?>'">

                        <!-- Icon -->
                        <div class="category-icon">
                            <?php if (!empty($category['icon'])): ?>
                                <img src="/courses-platform/upload/<?= rawurlencode(basename($category['icon'])) ?>"
                                    alt="<?= htmlspecialchars($category['name']) ?> icon">
                            <?php else: ?>
                                <i class="bi bi-code-slash fs-2 text-secondary"></i>
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="category-content">
                            <h4>
                                <?= htmlspecialchars($category['name']) ?>
                            </h4>
                        </div>

                        <!-- Admin buttons -->
                        <?php if ($isAdmin): ?>
                            <div class="category-actions" onclick="event.stopPropagation()">

                                <!-- Edit -->
                                <a href="edit.php?id=<?= $category['id'] ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <!-- Delete -->
                                <form action="" method="POST"
                                    onsubmit="event.stopPropagation(); return confirm('هل أنت متأكد من حذف التصنيف؟')">
                                    <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>

                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 border shadow-sm">
                    <i class="bi bi-folder-x fs-1 text-muted d-block mb-3"></i>
                    <h4 class="fw-bold">No Categories Found</h4>
                    <p class="text-muted mb-0">No categories have been added yet.</p>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php
include "../includes/footer.php";
?>