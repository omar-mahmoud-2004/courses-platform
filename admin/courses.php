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

// 3. معالجة حذف الكورس (Delete Course)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    if ($objcon->delete("courses", $delete_id)) {
        $success_msg = "Course and its associated data were deleted successfully.";
    } else {
        $error_msg = "Failed to delete course: " . $objcon->getError();
    }
}

// 4. جلب الكورسات بالتفاصيل باستخدام الدالة المخصصة
$courses = $objcon->getCoursesWithDetails() ?? [];

// 5. استدعاء الهيدر الموحد
include "../includes/header.php";
?>

<div class="container py-5">

    <!-- شريط التنقل والعنوان -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <small class="text-primary fw-bold text-uppercase">Admin Panel</small>
            <h1 class="fw-bold mt-2 mb-0">Courses Management</h1>
            <p class="text-secondary fs-6 mb-0">Monitor platform courses, instructors, pricing, and enrollments.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="users.php" class="btn btn-outline-primary">
                <i class="bi bi-people me-1"></i> Users
            </a>
            <a href="courses.php" class="btn btn-primary active">
                <i class="bi bi-book me-1"></i> Courses
            </a>
            <a href="categories.php" class="btn btn-outline-primary">
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

    <!-- بطاقة جدول الكورسات -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        
        <!-- شريط البحث والعداد -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-journal-code text-primary me-2"></i>All Courses (<?= count($courses) ?>)
            </h5>
            <div class="col-md-4">
                <input type="text" id="courseSearchInput" class="form-control form-control-sm" placeholder="Search by course, category or teacher...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="coursesTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Category</th>
                        <th>Instructor</th>
                        <th>Price</th>
                        <th>Lessons</th>
                        <th>Students</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= htmlspecialchars($course['id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center overflow-hidden" style="width: 48px; height: 48px; min-width: 48px;">
                                            <?php if (!empty($course['image'])): ?>
                                                <img src="/courses-platform/upload/<?= htmlspecialchars($course['image']) ?>" alt="Course" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='https://placehold.co/100x100?text=Course';">
                                            <?php else: ?>
                                                <i class="bi bi-journal-text text-muted fs-4"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block course-title"><?= htmlspecialchars($course['title']) ?></span>
                                            <small class="text-muted">Created: <?= date('M d, Y', strtotime($course['created_at'] ?? 'now')) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border course-category">
                                        <?= htmlspecialchars($course['category_name'] ?? 'Unassigned') ?>
                                    </span>
                                </td>
                                <td class="course-teacher">
                                    <span class="fw-semibold"><?= htmlspecialchars($course['teacher_name'] ?? 'Unknown') ?></span>
                                </td>
                                <td class="text-success fw-bold">
                                    <?= floatval($course['price'] ?? 0) > 0 ? '$' . number_format($course['price'], 2) : '<span class="text-primary">Free</span>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-play-circle me-1"></i> <?= $course['total_lessons'] ?? 0 ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-person-check me-1"></i> <?= $course['total_students'] ?? 0 ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- زر معاينة الكورس في الواجهة العامة -->
                                        <a href="/courses-platform/courses/details.php?id=<?= $course['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank" 
                                           title="Preview Course">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>

                                        <!-- زر حذف الكورس -->
                                        <a href="courses.php?action=delete&id=<?= $course['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this course? All associated lessons and enrollments will be deleted permanently.');" 
                                           title="Delete Course">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No courses available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- البحث الفوري عبر JavaScript -->
<script>
document.getElementById('courseSearchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#coursesTable tbody tr');

    rows.forEach(row => {
        let title = row.querySelector('.course-title')?.innerText.toLowerCase() || '';
        let category = row.querySelector('.course-category')?.innerText.toLowerCase() || '';
        let teacher = row.querySelector('.course-teacher')?.innerText.toLowerCase() || '';

        if (title.includes(filter) || category.includes(filter) || teacher.includes(filter)) {
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