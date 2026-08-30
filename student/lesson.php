
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

// بناخد رقم الدرس من الرابط
$lesson_id = (int) ($_GET['id'] ?? 0);

if ($lesson_id <= 0) {
    die("Lesson not found.");
}

// بنجيب الدرس ونتأكد إن الطالب مشترك في الكورس
$lessonData = $objCon->customQuery("
    SELECT
        lessons.*,
        courses.title AS course_title,
        courses.id AS course_id
    FROM lessons
    INNER JOIN courses
        ON lessons.course_id = courses.id
    INNER JOIN enrollments
        ON enrollments.course_id = courses.id
    WHERE lessons.id = $lesson_id
    AND enrollments.student_id = $student_id
    LIMIT 1
");

if (empty($lessonData)) {
    die("Lesson not found or you are not enrolled in this course.");
}

$lesson = $lessonData[0];

$course_id = (int) $lesson['course_id'];

// ==========================================
// لما الطالب يدوس Mark as Completed
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // نشوف هل الدرس متسجل قبل كده
    $checkProgress = $objCon->customQuery("
        SELECT id
        FROM progress
        WHERE student_id = $student_id
        AND lesson_id = $lesson_id
        LIMIT 1
    ");

    if (!empty($checkProgress)) {

        // لو موجود نحدثه
        $progress_id = (int) $checkProgress[0]['id'];

        $objCon->customQuery("
            UPDATE progress
            SET completed = 1
            WHERE id = $progress_id
        ");

    } else {

        // لو مش موجود نضيفه
        $objCon->customQuery("
            INSERT INTO progress
            (student_id, lesson_id, completed)
            VALUES
            ($student_id, $lesson_id, 1)
        ");
    }

    // نرجع لنفس الصفحة
    header("Location: lesson.php?id=$lesson_id&completed=success");
    exit;
}

// ==========================================
// نشوف الدرس Completed ولا لأ
// ==========================================

$completedData = $objCon->customQuery("
    SELECT id
    FROM progress
    WHERE student_id = $student_id
    AND lesson_id = $lesson_id
    AND completed = 1
    LIMIT 1
");

$isCompleted = !empty($completedData);

// بنجيب الـ Header
include "../includes/header.php";
?>

<!-- Font Awesome -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<main class="main-content flex-grow-1 w-100">

    <div class="container-fluid py-4 px-4 px-lg-5">

        <!-- رجوع للكورس -->
        <div class="mb-4">

            <a href="course.php?id=<?= $course_id ?>"
                class="text-decoration-none text-muted">

                <i class="fa-solid fa-arrow-left me-2"></i>
                Back to Course

            </a>

        </div>

        <!-- عنوان الدرس -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">

            <div class="card-body p-4 p-lg-5">

                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3">
                    <?= htmlspecialchars($lesson['course_title']) ?>
                </span>

                <h2 class="fw-bold mb-2">
                    <?= htmlspecialchars($lesson['title']) ?>
                </h2>

                <p class="text-muted mb-0">
                    Lesson <?= (int) $lesson['lesson_order'] ?>
                </p>

            </div>

        </div>

        <!-- محتوى الدرس -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">

            <div class="card-body p-4 p-lg-5">

                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-book-open me-2 text-primary"></i>
                    Lesson Content
                </h5>

                <div class="text-muted" style="line-height: 1.9;">

                    <?= nl2br(htmlspecialchars($lesson['content'])) ?>

                </div>

            </div>

        </div>

        <!-- Completed -->
        <div class="card border-0 shadow-sm rounded-3">

            <div class="card-body p-4">

                <?php if (isset($_GET['completed'])): ?>

                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        Lesson completed successfully!
                    </div>

                <?php endif; ?>

                <?php if ($isCompleted): ?>

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h6 class="fw-bold mb-1">
                                Lesson Completed
                            </h6>

                            <small class="text-muted">
                                You have completed this lesson.
                            </small>
                        </div>

                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fa-solid fa-check me-1"></i>
                            Completed
                        </span>

                    </div>

                <?php else: ?>

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h6 class="fw-bold mb-1">
                                Finished the lesson?
                            </h6>

                            <small class="text-muted">
                                Mark this lesson as completed.
                            </small>
                        </div>

                        <form method="POST">

                            <button type="submit"
                                class="btn btn-success rounded-3 px-4">

                                <i class="fa-solid fa-check me-2"></i>
                                Mark as Completed

                            </button>

                        </form>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>

<?php
// بنجيب الـ Footer
include "../includes/footer.php";
?>

