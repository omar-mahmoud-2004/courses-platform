<?php
// 1. بدء الجلسة بأمان
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. التحقق مرن وسريع من جلسة المدرس
$teacher_id = (int) ($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);
$role = $_SESSION['role'] ?? '';

// إذا لم يكن مسجلاً أو ليس مدرساً يتم التحويل للوجن
if ($teacher_id === 0 || $role !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

// 3. الاتصال بالداتابيز (الخروج خطوة للخلف من مجلد teacher)
require_once "../connect.php";
$objCon = new connect();

// ==========================================
// جلب الإحصائيات (خاصة بالمدرس الحالي فقط)
// ==========================================
$totalCourses = $objCon->countRecords("courses", "teacher_id = $teacher_id");
$totalStudents = $objCon->countRecords("users", "role = 'student'");

// ==========================================
// جلب الكورسات الخاصة بالمدرس الحالي فقط
// ==========================================
$sqlAllCourses = "SELECT courses.*, categories.name AS category_name 
                  FROM courses 
                  LEFT JOIN categories ON courses.category_id = categories.id 
                  WHERE courses.teacher_id = $teacher_id 
                  ORDER BY courses.id DESC";

$allCourses = $objCon->customQuery($sqlAllCourses);

// الهيدر
require_once "../includes/header.php";
?>

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous" />

<main class="main-content flex-grow-1 w-100 mt-0 pt-0">
    <div class="container-fluid pt-2 pb-4 px-4 px-lg-5">

        <!-- رسالة نجاح الإضافة -->
        <?php if (isset($_GET['add']) && $_GET['add'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show my-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> Course added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Welcome Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-2">
            <div>
                <span class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3 py-2 fw-semibold">
                    TEACHER PANEL
                </span>
                <h2 class="fw-bold mb-1">Welcome back, <?= htmlspecialchars($_SESSION['name'] ?? 'Professor') ?>! 👋
                </h2>
                <p class="text-muted mb-0">Here is an overview of your created courses.</p>
            </div>
            <a href="..\courses\add.php" class="btn btn-primary text-decoration-none px-4 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> Create New Course
            </a>
        </div>

        <!-- Stats Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Stat 1: My Courses -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center h-100 rounded-3 bg-white">
                    <div class="icon-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 50px; height: 50px; background-color: #eff6ff; color: #2563eb; font-size: 1.2rem;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-semibold">My Created Courses</span>
                        <h3 class="fw-bold mb-0"><?= number_format($totalCourses) ?></h3>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Total Students -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center h-100 rounded-3 bg-white">
                    <div class="icon-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 50px; height: 50px; background-color: #e0f2fe; color: #0284c7; font-size: 1.2rem;">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-semibold">Total Students</span>
                        <h3 class="fw-bold mb-0"><?= number_format($totalStudents) ?></h3>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Rating -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center h-100 rounded-3 bg-white">
                    <div class="icon-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 50px; height: 50px; background-color: #fef3c7; color: #d97706; font-size: 1.2rem;">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-semibold">Average Rating</span>
                        <h3 class="fw-bold mb-0">4.8 <span class="fs-6 text-muted fw-normal">/ 5.0</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Courses Table -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-white">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">My Courses</h5>
                <p class="text-muted small mb-0">List of courses created by you.</p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allCourses)): ?>
                            <?php foreach ($allCourses as $course): ?>
                                <?php
                                $imageField = !empty($course['image']) ? $course['image'] : ($course['thumbnail'] ?? '');
                                $imgPath = (!empty($imageField) && file_exists("../upload/" . $imageField))
                                    ? "../upload/" . $imageField
                                    : "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&q=80";
                                ?>
                                <tr>
                                    <td class="fw-semibold">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $imgPath ?>" class="course-img me-3 rounded"
                                                alt="<?= htmlspecialchars($course['title']) ?>"
                                                style="width: 45px; height: 45px; object-fit: cover;">
                                            <span><?= htmlspecialchars($course['title']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                                            <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-primary">$<?= number_format($course['price'], 2) ?></td>
                                    <td class="text-end">
                                        <a href="../courses/edit.php?id=<?= $course['id'] ?>"
                                            class="btn btn-sm btn-outline-secondary rounded-circle me-1"
                                            style="width: 32px; height: 32px; padding: 0; line-height: 30px;"
                                            title="Edit Course">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="../lessons/index.php?course_id=<?= $course['id'] ?>"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Manage Lessons">
                                            <i class="fa-solid fa-video me-1"></i> Lessons
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No courses found for your account. <a href="..\courses\add.php"
                                        class="text-primary fw-semibold">Add your first course!</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<?php require_once "../includes/footer.php"; ?>