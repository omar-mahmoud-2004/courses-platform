
<?php
// بنبدأ الـ Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// بنستدعي الداتابيز
require_once "../connect.php";
$objCon = new connect();

// لو الطالب مش عامل Login نرجعه للـ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = (int) $_SESSION['user_id'];

// بنجيب بيانات الطالب
$student = $objCon->selectone("users", $student_id);

if (empty($student)) {
    die("Student not found.");
}

// عدد الكورسات
$totalCourses = $objCon->countRecords(
    "enrollments",
    "student_id = $student_id"
);

// عدد الدروس المكتملة
$totalCompleted = $objCon->countRecords(
    "progress",
    "student_id = $student_id AND completed = 1"
);

// عدد كل الدروس
$totalLessonsData = $objCon->customQuery("
    SELECT COUNT(*) AS total
    FROM lessons
    INNER JOIN enrollments
        ON lessons.course_id = enrollments.course_id
    WHERE enrollments.student_id = $student_id
");

$totalLessons = !empty($totalLessonsData)
    ? (int)$totalLessonsData[0]['total']
    : 0;

// نسبة التقدم
$overallProgress = 0;

if ($totalLessons > 0) {
    $overallProgress = round(
        ($totalCompleted / $totalLessons) * 100
    );
}

// بنجيب الكورسات بتاعة الطالب
$myCourses = $objCon->customQuery("
    SELECT
        courses.id,
        courses.title,
        courses.image,
        categories.name AS category_name,

        (
            SELECT COUNT(*)
            FROM lessons
            WHERE lessons.course_id = courses.id
        ) AS total_lessons,

        (
            SELECT COUNT(*)
            FROM progress
            INNER JOIN lessons
                ON progress.lesson_id = lessons.id
            WHERE progress.student_id = $student_id
            AND progress.completed = 1
            AND lessons.course_id = courses.id
        ) AS completed_lessons

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

<!-- Font Awesome -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<main class="main-content flex-grow-1 w-100">

    <div class="container-fluid py-4 px-4 px-lg-5">

        <!-- عنوان الصفحة -->
        <div class="mb-4">

            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                MY PROGRESS
            </span>

            <h2 class="fw-bold mt-2 mb-1">
                Learning Progress 📊
            </h2>

            <p class="text-muted mb-0">
                Track your learning progress and completed lessons.
            </p>

        </div>

        <!-- الإحصائيات -->
        <div class="row g-4 mb-4">

            <!-- الكورسات -->
            <div class="col-12 col-md-4">

                <div class="card border-0 shadow-sm p-4 rounded-3 h-100">

                    <div class="d-flex align-items-center">

                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary me-3"
                            style="width:50px;height:50px;">

                            <i class="fa-solid fa-book-open"></i>

                        </div>

                        <div>

                            <span class="text-muted small fw-semibold">
                                My Courses
                            </span>

                            <h3 class="fw-bold mb-0">
                                <?= number_format($totalCourses) ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>

            <!-- الدروس المكتملة -->
            <div class="col-12 col-md-4">

                <div class="card border-0 shadow-sm p-4 rounded-3 h-100">

                    <div class="d-flex align-items-center">

                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success me-3"
                            style="width:50px;height:50px;">

                            <i class="fa-solid fa-circle-check"></i>

                        </div>

                        <div>

                            <span class="text-muted small fw-semibold">
                                Completed Lessons
                            </span>

                            <h3 class="fw-bold mb-0">
                                <?= number_format($totalCompleted) ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>

            <!-- التقدم -->
            <div class="col-12 col-md-4">

                <div class="card border-0 shadow-sm p-4 rounded-3 h-100">

                    <div class="d-flex align-items-center">

                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning me-3"
                            style="width:50px;height:50px;">

                            <i class="fa-solid fa-chart-line"></i>

                        </div>

                        <div>

                            <span class="text-muted small fw-semibold">
                                Overall Progress
                            </span>

                            <h3 class="fw-bold mb-0">
                                <?= $overallProgress ?>%
                            </h3>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- التقدم العام -->
        <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">

            <div class="d-flex justify-content-between align-items-center mb-2">

                <div>

                    <h5 class="fw-bold mb-1">
                        Overall Learning Progress
                    </h5>

                    <p class="text-muted small mb-0">
                        Your progress across all enrolled courses.
                    </p>

                </div>

                <span class="fw-bold text-primary">
                    <?= $overallProgress ?>%
                </span>

            </div>

            <div class="progress mt-3" style="height:10px;">

                <div class="progress-bar bg-primary"
                    style="width:<?= $overallProgress ?>%;">
                </div>

            </div>

            <div class="d-flex justify-content-between mt-3 text-muted small">

                <span>
                    <?= number_format($totalCompleted) ?> completed
                </span>

                <span>
                    <?= number_format($totalLessons) ?> total lessons
                </span>

            </div>

        </div>

        <!-- تقدم كل كورس -->
        <div class="card border-0 shadow-sm rounded-3 p-4">

            <div class="mb-4">

                <h5 class="fw-bold mb-1">
                    Course Progress
                </h5>

                <p class="text-muted small mb-0">
                    See your progress in each course.
                </p>

            </div>

            <?php if (!empty($myCourses)): ?>

                <?php foreach ($myCourses as $course): ?>

                    <?php
                    $total = (int)$course['total_lessons'];
                    $completed = (int)$course['completed_lessons'];

                    $courseProgress = 0;

                    if ($total > 0) {
                        $courseProgress = round(
                            ($completed / $total) * 100
                        );
                    }
                    ?>

                    <div class="border rounded-3 p-4 mb-3">

                        <div class="d-flex justify-content-between align-items-start mb-2">

                            <div>

                                <span class="badge bg-light text-dark border rounded-pill mb-2">
                                    <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                                </span>

                                <h5 class="fw-bold mb-1">
                                    <?= htmlspecialchars($course['title']) ?>
                                </h5>

                            </div>

                            <span class="fw-bold text-primary">
                                <?= $courseProgress ?>%
                            </span>

                        </div>

                        <div class="progress mb-2" style="height:8px;">

                            <div class="progress-bar bg-primary"
                                style="width:<?= $courseProgress ?>%;">
                            </div>

                        </div>

                        <div class="d-flex justify-content-between text-muted small">

                            <span>
                                <?= $completed ?> completed lessons
                            </span>

                            <span>
                                <?= $total ?> total lessons
                            </span>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <!-- لو مفيش كورسات -->
                <div class="text-center py-5">

                    <i class="fa-solid fa-chart-simple fa-3x text-muted mb-3"></i>

                    <h5 class="fw-bold">
                        No Progress Yet
                    </h5>

                    <p class="text-muted">
                        Enroll in a course to start tracking your progress.
                    </p>

                    <a href="../courses/index.php"
                        class="btn btn-primary rounded-3 px-4">

                        <i class="fa-solid fa-compass me-2"></i>
                        Explore Courses

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

<?php
// بنجيب الـ Footer
include "../includes/footer.php";
?>

