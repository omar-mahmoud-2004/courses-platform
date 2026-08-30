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

$error = "";

// 1. استقبال الـ course_id من الرابط (GET) أو من الفورم (POST)
$course_id = 0;
if (isset($_GET['course_id']) && (int) $_GET['course_id'] > 0) {
    $course_id = (int) $_GET['course_id'];
} elseif (isset($_POST['course_id']) && (int) $_POST['course_id'] > 0) {
    $course_id = (int) $_POST['course_id'];
}

// جلب اسم الكورس للتأكد
$course_name = "";
if ($course_id > 0) {
    $stmtCourse = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
    $stmtCourse->execute([$course_id]);
    $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);
    if ($course) {
        $course_name = $course['title'];
    }
}

// 2. عند الحفظ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = (int) $_POST['course_id'];
    $title = trim($_POST['title']);
    $duration = (int) $_POST['duration'];
    $video_url = trim($_POST['video_url']);

    // فحص ما إذا كان course_id وصل بشكل صحيح أم لا
    if ($course_id <= 0) {
        $error = "عفواً! لم يتم التعرّف على رقم الكورس (Course ID = 0). يرجى الدخول من صفحة الكورسات والضغط على زر إضافة درس الخاص بالحصة.";
    } else {
        // تنفيذ الإدخال
        $sql = "INSERT INTO lessons (title, duration, video_url, course_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $duration, $video_url, $course_id]);

        // التوجيه لصفحة الدروس الخاصة بالحصة
        header("Location: index.php?course_id=" . $course_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Lesson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow p-4 border-0 rounded-4">
                    <h2 class="mb-1 text-primary fw-bold">Add New Lesson</h2>

                    <!-- عرض تنبيه أحمر في حال عدم وجود course_id -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger my-3 py-2">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($course_name)): ?>
                        <p class="text-muted mb-4">Adding lesson to: <strong
                                class="text-dark"><?= htmlspecialchars($course_name) ?></strong></p>
                    <?php else: ?>
                        <p class="text-muted mb-4">Course ID: <strong>#<?= $course_id ?></strong></p>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- حقل مخفي يحمل ID الكورس -->
                        <input type="hidden" name="course_id" value="<?= $course_id ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lesson Title:</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Introduction to Variables" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Duration (Minutes):</label>
                            <input type="number" name="duration" class="form-control" placeholder="15" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Video URL:</label>
                            <input type="url" name="video_url" class="form-control"
                                placeholder="https://youtube.com/..." required>
                        </div>

                        <div class="d-flex gap-2 pt-2">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">Save Lesson</button>
                            <a href="index.php?course_id=<?= $course_id ?>"
                                class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
include "../includes/footer.php";
?>