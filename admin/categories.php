<?php
// 1. التحقق من الجلسة وصلاحية الآدمن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /courses-platform/auth/login.php");
    exit();
}

// 2. الاتصال بقاعدة البيانات
include "../connect.php";
$objcon = new connect();

$success_msg = "";
$error_msg = "";

// 3. إضافة تصنيف جديد (Create)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    if (!empty($name)) {
        if ($objcon->insert(['name' => $name], "categories")) {
            $success_msg = "Category added successfully.";
        } else {
            $error_msg = "Failed to add category: " . $objcon->getError();
        }
    } else {
        $error_msg = "Category name cannot be empty.";
    }
}

// 4. تعديل تصنيف (Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $cat_id = intval($_POST['category_id']);
    $name = trim($_POST['name'] ?? '');
    if (!empty($name) && $cat_id > 0) {
        if ($objcon->update(['name' => $name], "categories", $cat_id)) {
            $success_msg = "Category updated successfully.";
        } else {
            $error_msg = "Failed to update category: " . $objcon->getError();
        }
    } else {
        $error_msg = "Category name cannot be empty.";
    }
}

// 5. حذف تصنيف (Delete)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    if ($objcon->delete("categories", $delete_id)) {
        $success_msg = "Category deleted successfully.";
    } else {
        $error_msg = "Failed to delete category: " . $objcon->getError();
    }
}

// 6. جلب التصنيفات مع عدد الكورسات التابعة لها
$categories = $objcon->getCategoriesWithCount() ?? [];

// 7. استدعاء الهيدر الموحد
include "../includes/header.php";
?>

<div class="container py-5">

    <!-- شريط التنقل والعنوان -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <small class="text-primary fw-bold text-uppercase">Admin Panel</small>
            <h1 class="fw-bold mt-2 mb-0">Categories Management</h1>
            <p class="text-secondary fs-6 mb-0">Create, rename, and organize tracks and course categories.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="users.php" class="btn btn-outline-primary">
                <i class="bi bi-people me-1"></i> Users
            </a>
            <a href="courses.php" class="btn btn-outline-primary">
                <i class="bi bi-book me-1"></i> Courses
            </a>
            <a href="categories.php" class="btn btn-primary active">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            <a href="reviews.php" class="btn btn-outline-primary">
                <i class="bi bi-star me-1"></i> Reviews
            </a>
        </div>
    </div>

    <!-- رسائل التنبيه -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- فورم إضافة تصنيف جديد -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Add New Category</h5>
                <form method="POST" action="categories.php">
                    <div class="mb-3">
                        <label for="catName" class="form-label text-muted fw-semibold">Category Name</label>
                        <input type="text" name="name" id="catName" class="form-control" placeholder="e.g. Web Development" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-folder-plus me-1"></i> Save Category
                    </button>
                </form>
            </div>
        </div>

        <!-- جدول استعراض وتعديل وحذف التصنيفات -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-tags-fill text-primary me-2"></i>All Categories (<?= count($categories) ?>)
                    </h5>
                    <div class="col-md-5">
                        <input type="text" id="categorySearchInput" class="form-control form-control-sm" placeholder="Search category...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="categoriesTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Courses Linked</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= htmlspecialchars($category['id']) ?></td>
                                        <td>
                                            <span class="fw-semibold category-title">
                                                <?= htmlspecialchars($category['name']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border">
                                                <i class="bi bi-journal-code me-1"></i> <?= $category['total_courses'] ?? 0 ?> Courses
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- زر فتح مودال التعديل -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning edit-btn" 
                                                        data-id="<?= $category['id'] ?>" 
                                                        data-name="<?= htmlspecialchars($category['name']) ?>" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCategoryModal"
                                                        title="Edit Category">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- زر الحذف -->
                                                <a href="categories.php?action=delete&id=<?= $category['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this category? Associated courses will also be affected.');"
                                                   title="Delete Category">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No categories found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Modal تعديل التصنيف -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="categories.php">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" name="category_id" id="editModalCatId">
                    <div class="mb-3">
                        <label for="editModalCatName" class="form-label text-muted fw-semibold">Category Name</label>
                        <input type="text" name="name" id="editModalCatName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_category" class="btn btn-primary fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- كود JavaScript للبحث وتمرير بيانات المودال -->
<script>
// تمرير بيانات التصنيف تلقائياً إلى نافذة الـ Modal عند الضغط على زر التعديل
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('editModalCatId').value = this.getAttribute('data-id');
        document.getElementById('editModalCatName').value = this.getAttribute('data-name');
    });
});

// البحث الفوري داخل الجدول
document.getElementById('categorySearchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#categoriesTable tbody tr');

    rows.forEach(row => {
        let name = row.querySelector('.category-title')?.innerText.toLowerCase() || '';
        if (name.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php
include "../includes/footer.php";
?>