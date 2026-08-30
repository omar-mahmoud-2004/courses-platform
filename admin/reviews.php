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

// 3. معالجة حذف التقييم (Delete Review)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    if ($objcon->delete("reviews", $delete_id)) {
        $success_msg = "Review deleted successfully.";
    } else {
        $error_msg = "Failed to delete review: " . $objcon->getError();
    }
}

// 4. جلب التقييمات بالتفاصيل (اسم الطالب وعنوان الكورس)
$reviews = $objcon->getReviewsWithDetails() ?? [];

// 5. استدعاء الهيدر الموحد
include "../includes/header.php";
?>

<div class="container py-5">

    <!-- شريط التنقل والعنوان -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <small class="text-primary fw-bold text-uppercase">Admin Panel</small>
            <h1 class="fw-bold mt-2 mb-0">Reviews Management</h1>
            <p class="text-secondary fs-6 mb-0">Monitor student feedback, course ratings, and moderate comments.</p>
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
            <a href="categories.php" class="btn btn-outline-primary">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            <a href="reviews.php" class="btn btn-primary active">
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

    <!-- بطاقة جدول التقييمات -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        
        <!-- شريط البحث والعداد -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-star-fill text-warning me-2"></i>All Reviews (<?= count($reviews) ?>)
            </h5>
            <div class="col-md-4">
                <input type="text" id="reviewSearchInput" class="form-control form-control-sm" placeholder="Search by student, course, or comment...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="reviewsTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $rev): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= htmlspecialchars($rev['id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                        <span class="fw-semibold rev-student"><?= htmlspecialchars($rev['student_name'] ?? 'Student #' . $rev['student_id']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rev-course">
                                        <i class="bi bi-journal-bookmark text-primary me-1"></i>
                                        <?= htmlspecialchars($rev['course_title'] ?? 'Course #' . $rev['course_id']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-warning text-nowrap">
                                        <?php
                                        $rating = intval($rev['rating'] ?? 5);
                                        for ($i = 1; $i <= 5; $i++):
                                            if ($i <= $rating): ?>
                                                <i class="bi bi-star-fill"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star text-muted opacity-25"></i>
                                            <?php endif;
                                        endfor;
                                        ?>
                                        <span class="ms-1 text-dark fw-bold small">(<?= $rating ?>/5)</span>
                                    </div>
                                </td>
                                <td style="max-width: 320px;">
                                    <p class="mb-0 text-muted small text-truncate rev-comment" title="<?= htmlspecialchars($rev['comment'] ?? '') ?>">
                                        <?= htmlspecialchars($rev['comment'] ?? 'No written comment.') ?>
                                    </p>
                                </td>
                                <td class="text-center">
                                    <a href="reviews.php?action=delete&id=<?= $rev['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this review?');"
                                       title="Delete Review">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No reviews submitted yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- البحث الفوري عبر JavaScript -->
<script>
document.getElementById('reviewSearchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#reviewsTable tbody tr');

    rows.forEach(row => {
        let student = row.querySelector('.rev-student')?.innerText.toLowerCase() || '';
        let course = row.querySelector('.rev-course')?.innerText.toLowerCase() || '';
        let comment = row.querySelector('.rev-comment')?.innerText.toLowerCase() || '';

        if (student.includes(filter) || course.includes(filter) || comment.includes(filter)) {
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