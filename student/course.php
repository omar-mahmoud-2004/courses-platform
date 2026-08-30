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

// بناخد رقم الكورس من الرابط
$course_id = (int) ($_GET['id'] ?? 0);

if ($course_id <= 0) {
    die("Course not found.");
}

// بنجيب الكورس بشرط إن الطالب مشترك فيه
$courseData = $objCon->customQuery("
    SELECT courses.*, categories.name AS category_name
    FROM courses
    LEFT JOIN categories
        ON courses.category_id = categories.id
    INNER JOIN enrollments
        ON enrollments.course_id = courses.id
    WHERE courses.id = $course_id
    AND enrollments.student_id = $student_id
    LIMIT 1
");

if (empty($courseData)) {
    die("You are not enrolled in this course.");
}

$course = $courseData[0];

// بنجيب دروس الكورس
$lessons = $objCon->customQuery("
    SELECT *
    FROM lessons
    WHERE course_id = $course_id
    ORDER BY lesson_order ASC
");

// بنجيب الـ Header
include "../includes/header.php";
?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<main class="main-content flex-grow-1 w-100">

    <div class="container-fluid py-4 px-4 px-lg-5">

        <!-- بيانات الكورس -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">

            <div class="row g-0">

                <!-- صورة الكورس -->
                <div class="col-md-4">

                    <?php
                    $image = $course['image'] ?? '';
                    $imagePath = "../upload/" . $image;
                    ?>

                    <?php if (!empty($image) && file_exists($imagePath)): ?>

                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($course['title']) ?>"
                            style="width:100%;height:280px;object-fit:cover;">

                    <?php else: ?>

                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:280px;">

                            <i class="fa-solid fa-book-open fa-4x text-muted"></i>

                        </div>

                    <?php endif; ?>

                </div>

                <!-- تفاصيل الكورس -->
                <div class="col-md-8">

                    <div class="card-body p-4 p-lg-5">

                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3">
                            <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                        </span>

                        <h2 class="fw-bold mb-3">
                            <?= htmlspecialchars($course['title']) ?>
                        </h2>

                        <p class="text-muted mb-4">
                            <?= nl2br(htmlspecialchars($course['description'])) ?>
                        </p>

                        <div class="d-flex gap-4 text-muted">

                            <span>
                                <i class="fa-solid fa-book me-2"></i>
                                <?= count($lessons) ?> Lessons
                            </span>

                            <span>
                                <i class="fa-solid fa-dollar-sign me-2"></i>
                                <?= number_format((float) $course['price'], 2) ?>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- الدروس -->
        <div class="card border-0 shadow-sm rounded-3 p-4">

            <div class="mb-4">

                <h4 class="fw-bold mb-1">
                    Course Lessons
                </h4>

                <p class="text-muted small mb-0">
                    Start learning by choosing a lesson.
                </p>

            </div>

            <?php if (!empty($lessons)): ?>

                <div class="list-group">

                    <?php foreach ($lessons as $index => $lesson): ?>

                        <a href="lesson.php?id=<?= $lesson['id'] ?>"
                            class="list-group-item list-group-item-action border-0 shadow-sm rounded-3 mb-2 p-3">

                            <div class="d-flex align-items-center">

                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary me-3"
                                    style="width:45px;height:45px;">

                                    <i class="fa-solid fa-play"></i>

                                </div>

                                <div>

                                    <h6 class="fw-bold mb-1">
                                        Lesson <?= $index + 1 ?>:
                                        <?= htmlspecialchars($lesson['title']) ?>
                                    </h6>

                                    <small class="text-muted">
                                        Click to start this lesson
                                    </small>

                                </div>

                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="text-center py-5">

                    <i class="fa-solid fa-video-slash fa-3x text-muted mb-3"></i>

                    <h5 class="fw-bold">
                        No Lessons Yet
                    </h5>

                    <p class="text-muted mb-0">
                        This course doesn't have any lessons yet.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

<?php
// بنجيب الـ Footer
include "../includes/footer.php";
?>