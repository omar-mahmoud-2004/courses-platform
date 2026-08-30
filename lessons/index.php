<?php
include "../includes/header.php";

$host = 'localhost';
$db = 'courses-platform';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 1. استقبال رقم الكورس من الرابط
$course_id = isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;

// 2. جلب اسم الكورس
$course_name = "";
if ($course_id > 0) {
    $stmtCourse = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
    $stmtCourse->execute([$course_id]);
    $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);
    if ($course) {
        $course_name = $course['title'];
    }
}

// 3. جلب كافة الدروس الخاصة بهذا الكورس فقط
$lessons = [];
if ($course_id > 0) {
    $stmtLessons = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY id ASC");
    $stmtLessons->execute([$course_id]);
    $lessons = $stmtLessons->fetchAll(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Lessons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= !empty($course_name) ? htmlspecialchars($course_name) : "Course Lessons" ?>
                </h2>
                <p class="text-muted mb-0">Manage all lessons created for this course.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="../courses/index.php" class="btn btn-outline-secondary rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Courses
                </a>
               <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student'): ?>
                       <!-- زر الإضافة يظهر لأي شخص مسجل دخول ما عدا الطالب -->
                       <a href="add.php?course_id=<?= $course_id ?>" class="btn btn-primary px-4 rounded-3 shadow-sm">
                         <i class="fa-solid fa-plus me-1"></i> Add New Lesson
                       </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Lesson Title</th>
                            <th>Duration</th>
                            <th>Video Link</th>
                            <th class="text-center" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lessons)): ?>
                            <?php foreach ($lessons as $index => $lesson): ?>
                                <tr>
                                    <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td class="fw-semibold text-dark">
                                        <?= htmlspecialchars($lesson['title'] ?? 'Untitled Lesson') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fa-regular fa-clock me-1"></i> <?= (int) ($lesson['duration'] ?? 0) ?>
                                            mins
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($lesson['video_url'])): ?>
                                            <a href="<?= htmlspecialchars($lesson['video_url']) ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="fa-solid fa-play me-1"></i> Watch Video
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">No link</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- عمود الأزرار Edit و Delete -->
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- زر التعديل -->
                                            <a href="edit.php?id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>"
                                                class="btn btn-sm btn-light rounded-circle text-secondary shadow-sm"
                                                title="Edit Lesson">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <!-- زر الحذف -->
                                            <a href="delete.php?id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>"
                                                class="btn btn-sm btn-light rounded-circle text-danger shadow-sm"
                                                title="Delete Lesson"
                                                onclick="return confirm('Are you sure you want to delete this lesson?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-video-slash fs-1 d-block mb-3 text-secondary"></i>
                                    <h5>No lessons added yet for this course.</h5>
                                    <p class="small">Click the button above to add your first lesson!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
include "../includes/footer.php";
?>