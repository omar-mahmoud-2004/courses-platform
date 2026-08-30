<?php
// بنبدأ الـ Session عشان نعرف الطالب اللي عامل Login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// بنستدعي ملف الاتصال بالداتابيز
require_once "../connect.php";
$objCon = new connect();

// لو مفيش طالب عامل Login نرجعه لصفحة الـ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = (int) $_SESSION['user_id'];

// بنجيب بيانات الطالب من جدول users
$student = $objCon->selectone("users", $student_id);

if (empty($student)) {
    die("Student not found.");
}

// =========================
// إحصائيات الطالب
// =========================

// عدد الكورسات اللي الطالب مشترك فيها
$totalCourses = $objCon->countRecords(
    "enrollments",
    "student_id = $student_id"
);

// عدد الدروس اللي الطالب خلصها
$totalCompleted = $objCon->countRecords(
    "progress",
    "student_id = $student_id AND completed = 1"
);

// عدد كل الدروس الموجودة في الكورسات بتاعة الطالب
$totalLessonsData = $objCon->customQuery("
    SELECT COUNT(*) AS total
    FROM lessons
    INNER JOIN enrollments
        ON lessons.course_id = enrollments.course_id
    WHERE enrollments.student_id = $student_id
");

$totalLessons = !empty($totalLessonsData)
    ? (int) $totalLessonsData[0]['total']
    : 0;

// بنحسب نسبة التقدم
$progress = 0;

if ($totalLessons > 0) {
    $progress = round(($totalCompleted / $totalLessons) * 100);
}

// =========================
// الكورسات بتاعة الطالب
// =========================

$myCourses = $objCon->customQuery("
    SELECT
        courses.*,
        categories.name AS category_name
    FROM enrollments
    INNER JOIN courses
        ON enrollments.course_id = courses.id
    LEFT JOIN categories
        ON courses.category_id = categories.id
    WHERE enrollments.student_id = $student_id
    ORDER BY enrollments.id DESC
");

// بنجيب الـ Header
include "../includes/header.php";
?>

<!-- Font Awesome عشان الأيقونات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer">

<!-- المحتوى الرئيسي -->
<main class="main-content flex-grow-1 w-100 mt-0 pt-0">
    <div class="container-fluid pt-2 pb-4 px-4 px-lg-5">

        <!-- الترحيب بالطالب -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-2">
            <div>
                <span class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3 py-2 fw-semibold">
                    STUDENT PANEL
                </span>

                <h2 class="fw-bold mb-1">
                    Welcome back, <?= htmlspecialchars($student['name']) ?>! 👋
                </h2>

                <p class="text-muted mb-0">
                    Continue learning and track your progress today.
                </p>
            </div>

            <a href="../courses/index.php" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-compass me-2"></i>
                Explore Courses
            </a>
        </div>

        <!-- كروت الإحصائيات -->
        <div class="row g-4 mb-4">

            <!-- عدد الكورسات -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center h-100 rounded-3 bg-white">
                    <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
                        style="width:50px;height:50px;background-color:#eff6ff;color:#2563eb;font-size:1.2rem;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                    <div>
                        <span class="text-muted d-block small fw-semibold">
                            My Courses
                        </span>
                        <h3 class="fw-bold mb-0">
                            <?= number_format($totalCourses) ?>
                        </h3>
                    </div>
                </div>
            </div>

            <!-- الدروس المكتملة -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center h-100 rounded-3 bg-white">
                    <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
                        style="width:50px;height:50px;background-color:#ecfdf5;color:#059669;font-size:1.2rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <span class="text-muted d-block small fw-semibold">
                            Completed Lessons
                        </span>
                        <h3 class="fw-bold mb-0">
                            <?= number_format($totalCompleted) ?>
                        </h3>
                    </div>
                </div>
            </div>

            <!-- نسبة التقدم -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center h-100 rounded-3 bg-white">
                    <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
                        style="width:50px;height:50px;background-color:#fef3c7;color:#d97706;font-size:1.2rem;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>

                    <div>
                        <span class="text-muted d-block small fw-semibold">
                            Overall Progress
                        </span>
                        <h3 class="fw-bold mb-0">
                            <?= $progress ?>%
                        </h3>
                    </div>
                </div>
            </div>

        </div>

        <!-- الاختصارات -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-white">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Quick Actions</h5>
                <p class="text-muted small mb-0">
                    Quickly access your learning tools.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">

                <a href="my-courses.php" class="btn btn-primary rounded-3 px-4">
                    <i class="fa-solid fa-book me-2"></i>
                    My Courses
                </a>

                <a href="progress.php" class="btn btn-success rounded-3 px-4">
                    <i class="fa-solid fa-chart-simple me-2"></i>
                    My Progress
                </a>

                <a href="quiz.php" class="btn btn-warning rounded-3 px-4">
                    <i class="fa-solid fa-clipboard-question me-2"></i>
                    Quizzes
                </a>

                <a href="profile.php" class="btn btn-outline-secondary rounded-3 px-4">
                    <i class="fa-solid fa-user me-2"></i>
                    Profile
                </a>

            </div>
        </div>

        <!-- الكورسات بتاعة الطالب -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-white">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">My Courses</h5>
                    <p class="text-muted small mb-0">
                        Courses you are currently enrolled in.
                    </p>
                </div>

                <a href="my-courses.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    View All
                </a>
            </div>

            <div class="row g-4">

                <?php if (!empty($myCourses)): ?>

                    <?php foreach ($myCourses as $course): ?>

                        <?php
                        // بنحدد مكان صورة الكورس
                        $image = $course['image'] ?? '';
                        $imagePath = "../upload/" . $image;
                        ?>

                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">

                                <!-- صورة الكورس -->
                                <?php if (!empty($image) && file_exists($imagePath)): ?>

                                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($course['title']) ?>"
                                        style="width:100%;height:180px;object-fit:cover;">

                                <?php else: ?>

                                    <!-- لو مفيش صورة بنحط أيقونة -->
                                    <div class="d-flex align-items-center justify-content-center bg-light"
                                        style="width:100%;height:180px;">
                                        <i class="fa-solid fa-book-open fa-3x text-muted"></i>
                                    </div>

                                <?php endif; ?>

                                <div class="card-body">

                                    <!-- اسم التصنيف -->
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill border mb-2">
                                        <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                                    </span>

                                    <!-- اسم الكورس -->
                                    <h5 class="fw-bold mb-2">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </h5>

                                    <!-- وصف الكورس -->
                                    <p class="text-muted small mb-3">
                                        <?= htmlspecialchars(substr($course['description'] ?? '', 0, 100)) ?>...
                                    </p>

                                    <!-- Progress -->
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small text-muted">
                                            Course Progress
                                        </span>

                                        <span class="small fw-bold text-primary">
                                            <?= $progress ?>%
                                        </span>
                                    </div>

                                    <div class="progress mb-3" style="height:7px;">
                                        <div class="progress-bar bg-primary" style="width:<?= $progress ?>%;">
                                        </div>
                                    </div>

                                    <!-- دخول الكورس -->
                                    <a href="course.php?id=<?= $course['id'] ?>" class="btn btn-primary w-100 rounded-3">
                                        <i class="fa-solid fa-play me-2"></i>
                                        Continue Learning
                                    </a>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <!-- لو الطالب مش مشترك في أي كورس -->
                    <div class="col-12">
                        <div class="text-center py-5 bg-light rounded-3">

                            <i class="fa-solid fa-book-open fa-3x text-muted mb-3"></i>

                            <h5 class="fw-bold">
                                No Courses Yet
                            </h5>

                            <p class="text-muted">
                                You haven't enrolled in any course yet.
                            </p>

                            <a href="../categories/index.php" class="btn btn-primary rounded-3 px-4">
                                <i class="fa-solid fa-compass me-2"></i>
                                Explore Courses
                            </a>

                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <!-- التقدم العام -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-white">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="fw-bold mb-1">
                        Learning Progress
                    </h5>

                    <p class="text-muted small mb-0">
                        Track your overall learning progress.
                    </p>
                </div>

                <h5 class="fw-bold text-primary mb-0">
                    <?= $progress ?>%
                </h5>
            </div>

            <!-- شريط التقدم -->
            <div class="progress mt-3" style="height:10px;">
                <div class="progress-bar bg-primary" style="width:<?= $progress ?>%;">
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3 text-muted small">

                <span>
                    <?= number_format($totalCompleted) ?> completed lessons
                </span>

                <span>
                    <?= number_format($totalLessons) ?> total lessons
                </span>

            </div>

        </div>

    </div>
</main>

<?php
// بنجيب الـ Footer
include "../includes/footer.php";
?>