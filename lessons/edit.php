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

// 1. استقبال ID الدرس و ID الكورس
$id = $_GET['id'] ?? null;
$course_id = $_GET['course_id'] ?? null;

if (!$id) {
    header("Location: index.php?course_id=" . $course_id);
    exit();
}

// 2. معالجة تحديث البيانات عند إرسال الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $duration = (int) $_POST['duration'];
    $video_url = trim($_POST['video_url']);
    $course_id = (int) $_POST['course_id'];

    // تحديث كافة حقول الدرس
    $sql = "UPDATE lessons SET title = ?, duration = ?, video_url = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $duration, $video_url, $id]);

    // العودة لصفحة دروس الكورس
    header("Location: index.php?course_id=" . $course_id);
    exit();
}

// 3. جلب بيانات الدرس الحالية للعرض في المدخلات
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    header("Location: index.php?course_id=" . $course_id);
    exit();
}

// تحديد course_id المعتمد للعودة
$current_course_id = $course_id ?? $lesson['course_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lesson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- Top Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">Edit Lesson</h2>
                <p class="text-muted mb-0">Update information for this lesson.</p>
            </div>

            <div>
                <a href="index.php?course_id=<?= $current_course_id ?>" class="btn btn-outline-secondary rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Lessons
                </a>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">

                    <form method="POST" action="">
                        <!-- حفظ course_id للتحويل عليه بعد الحفظ -->
                        <input type="hidden" name="course_id" value="<?= $current_course_id ?>">

                        <!-- عنوان الدرس -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Lesson Title</label>
                            <input type="text" name="title" class="form-control form-control-lg fs-6"
                                value="<?= htmlspecialchars($lesson['title'] ?? ''); ?>" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- مدة الدرس -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Duration (Minutes)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-regular fa-clock"></i>
                                    </span>
                                    <input type="number" name="duration"
                                        class="form-control form-control-lg fs-6 border-start-0 ps-0"
                                        value="<?= (int) ($lesson['duration'] ?? 0); ?>" required>
                                </div>
                            </div>

                            <!-- رابط الفيديو -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Video URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-link"></i>
                                    </span>
                                    <input type="url" name="video_url"
                                        class="form-control form-control-lg fs-6 border-start-0 ps-0"
                                        value="<?= htmlspecialchars($lesson['video_url'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ والإلغاء -->
                        <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                            <a href="index.php?course_id=<?= $current_course_id ?>"
                                class="btn btn-light px-4 rounded-3 border">Cancel</a>
                            <button type="submit"
                                class="btn btn-warning text-dark fw-semibold px-4 rounded-3 shadow-sm">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Update Lesson
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
include "../includes/footer.php";
?>