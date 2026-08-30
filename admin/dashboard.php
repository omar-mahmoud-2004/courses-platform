<?php
// 1. التحقق من الصلاحية
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /courses-platform/auth/login.php");
    exit();
}

// 2. استدعاء ملف الاتصال
include "../connect.php";
$objcon = new connect();

// 3. جلب الإحصائيات باستخدام دالة countRecords المخصصة
$total_students    = $objcon->countRecords("users", "role = 'student'");
$total_teachers    = $objcon->countRecords("users", "role = 'teacher'");
$total_courses     = $objcon->countRecords("courses");
$total_enrollments = $objcon->countRecords("enrollments");
$total_categories  = $objcon->countRecords("categories");
$total_reviews     = $objcon->countRecords("reviews");

// 4. جلب آخر 5 مستخدمين وآخر 5 كورسات
$recent_users   = $objcon->customQuery("SELECT * FROM users ORDER BY id DESC LIMIT 5");
$all_courses    = $objcon->getCoursesWithDetails();
$recent_courses = array_slice($all_courses, 0, 5);

// 5. استدعاء الهيدر
include "../includes/header.php";
?>

<div class="container py-5">

    <!-- العنوان والتنقل -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <small class="text-primary fw-bold text-uppercase">Admin Panel</small>
            <h1 class="fw-bold mt-2 mb-0">Dashboard Overview</h1>
            <p class="text-secondary fs-6 mb-0">Manage platform users, courses, categories and reviews.</p>
        </div>

        <!-- أزرار التنقل السريع للآدمن -->
        <div class="d-flex gap-2 flex-wrap">
            <a href="dashboard.php" class="btn btn-primary active">
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
            <a href="reviews.php" class="btn btn-outline-primary">
                <i class="bi bi-star me-1"></i> Reviews
            </a>
        </div>
    </div>

    <!-- كروت الإحصائيات (Stat Cards) -->
    <div class="row g-4 mb-5">
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fw-semibold">Students</span>
                        <h2 class="fw-bold mb-0 text-primary mt-2"><?= $total_students ?></h2>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fw-semibold">Teachers</span>
                        <h2 class="fw-bold mb-0 text-success mt-2"><?= $total_teachers ?></h2>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fw-semibold">Total Courses</span>
                        <h2 class="fw-bold mb-0 text-info mt-2"><?= $total_courses ?></h2>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                        <i class="bi bi-journal-code"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fw-semibold">Enrollments</span>
                        <h2 class="fw-bold mb-0 text-warning mt-2"><?= $total_enrollments ?></h2>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fw-semibold">Categories</span>
                        <h2 class="fw-bold mb-0 text-secondary mt-2"><?= $total_categories ?></h2>
                    </div>
                    <div class="bg-secondary-subtle text-secondary p-3 rounded-circle fs-3">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted fw-semibold">Reviews</span>
                        <h2 class="fw-bold mb-0 text-danger mt-2"><?= $total_reviews ?></h2>
                    </div>
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle fs-3">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جداول آخر النشاطات -->
    <div class="row g-4">
        <!-- آخر المستخدمين -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Recent Users</h5>
                    <a href="users.php" class="text-primary fw-semibold text-decoration-none">View All →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_users)): ?>
                                <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($user['name'] ?? '') ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></small></td>
                                    <td>
                                        <span class="badge <?= ($user['role'] ?? '') === 'admin' ? 'bg-danger' : (($user['role'] ?? '') === 'teacher' ? 'bg-success' : 'bg-primary') ?>">
                                            <?= ucfirst($user['role'] ?? 'student') ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- آخر الكورسات -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Recent Courses</h5>
                    <a href="courses.php" class="text-primary fw-semibold text-decoration-none">View All →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Instructor</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_courses)): ?>
                                <?php foreach ($recent_courses as $course): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($course['title'] ?? '') ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($course['teacher_name'] ?? 'N/A') ?></small></td>
                                    <td class="text-success fw-bold">$<?= number_format($course['price'] ?? 0, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted">No courses found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include "../includes/footer.php";
?>