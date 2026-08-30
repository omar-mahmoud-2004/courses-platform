
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

// بنجيب الكورسات اللي الطالب مشترك فيها
$myCourses = $objCon->customQuery("
    SELECT courses.*, categories.name AS category_name
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

<!-- أيقونات Font Awesome -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<main class="main-content flex-grow-1 w-100">

    <div class="container-fluid py-4 px-4 px-lg-5">

        <!-- عنوان الصفحة -->
        <div class="mb-4">

            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                MY LEARNING
            </span>

            <h2 class="fw-bold mt-2 mb-1">
                My Courses 📚
            </h2>

            <p class="text-muted mb-0">
                Courses you are currently enrolled in.
            </p>

        </div>

        <!-- الكورسات -->
        <div class="row g-4">

            <?php if (!empty($myCourses)): ?>

                <?php foreach ($myCourses as $course): ?>

                    <?php
                    // بنحدد صورة الكورس
                    $image = $course['image'] ?? '';
                    $imagePath = "../upload/" . $image;
                    ?>

                    <div class="col-12 col-md-6 col-lg-4">

                        <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">

                            <!-- صورة الكورس -->
                            <?php if (!empty($image) && file_exists($imagePath)): ?>

                                <img src="<?= htmlspecialchars($imagePath) ?>"
                                    alt="<?= htmlspecialchars($course['title']) ?>"
                                    style="width:100%;height:190px;object-fit:cover;">

                            <?php else: ?>

                                <div class="d-flex align-items-center justify-content-center bg-light"
                                    style="height:190px;">

                                    <i class="fa-solid fa-book-open fa-3x text-muted"></i>

                                </div>

                            <?php endif; ?>

                            <div class="card-body p-4">

                                <!-- التصنيف -->
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 mb-2">
                                    <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                                </span>

                                <!-- اسم الكورس -->
                                <h5 class="fw-bold mb-2">
                                    <?= htmlspecialchars($course['title']) ?>
                                </h5>

                                <!-- وصف الكورس -->
                                <p class="text-muted small mb-3">
                                    <?= htmlspecialchars(
                                        substr($course['description'] ?? '', 0, 120)
                                    ) ?>...
                                </p>

                                <!-- السعر -->
                                <div class="mb-3">
                                    <span class="fw-bold text-primary">
                                        $<?= number_format((float)$course['price'], 2) ?>
                                    </span>
                                </div>

                                <!-- زر الدخول للكورس -->
                                <a href="course.php?id=<?= $course['id'] ?>"
                                    class="btn btn-primary w-100 rounded-3">

                                    <i class="fa-solid fa-play me-2"></i>
                                    Continue Learning

                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <!-- لو مفيش كورسات -->
                <div class="col-12">

                    <div class="card border-0 shadow-sm text-center py-5">

                        <i class="fa-solid fa-book-open fa-3x text-muted mb-3"></i>

                        <h4 class="fw-bold">
                            No Courses Yet
                        </h4>

                        <p class="text-muted">
                            You haven't enrolled in any course yet.
                        </p>

                        <div>
                            <a href="../courses/index.php"
                                class="btn btn-primary rounded-3 px-4">

                                <i class="fa-solid fa-compass me-2"></i>
                                Explore Courses

                            </a>
                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

<?php
// بنجيب الـ Footer
include "../includes/footer.php";
?>

