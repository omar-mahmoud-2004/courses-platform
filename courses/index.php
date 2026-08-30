<?php
// 1. الاتصال بقاعدة البيانات والجلسة
include "../connect.php";
$objCon = new connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. الحماية: التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: /courses-platform/auth/login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// 3. تحديد منطق العرض والاستعلام حسب نوع المستخدم (Role)
$whereClause = "";
$pageTitle = "My Courses";
$pageSubTitle = "Manage and view all courses created by you.";

if ($role === 'teacher') {
    // 🔥 المدرس: يجلب كورســاته فقط
    $whereClause = " WHERE courses.teacher_id = $user_id ";
} else if ($role === 'student') {
    // 🔥 الطالب: يجب أن يحدد Category أولاً
    if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $cat_id = (int) $_GET['category_id'];
        $whereClause = " WHERE courses.category_id = $cat_id ";

        // جلب اسم القسم لعنوان الصفحة
        $catData = $objCon->customQuery("SELECT name FROM categories WHERE id = $cat_id");
        $catName = !empty($catData) ? $catData[0]['name'] : 'Selected';
        $pageTitle = htmlspecialchars($catName) . " Courses";
        $pageSubTitle = "Explore available courses in this category.";
    } else {
        // إذا كان طالباً ولم يحدد قسم، نعيده لصفحة التصنيفات ليختار منها
        header("Location: ../categories/index.php");
        exit();
    }
} else if ($role === 'admin') {
    // الآدمن: إذا اختر قسم يعرضه، وإلا يعرض الكل
    if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $cat_id = (int) $_GET['category_id'];
        $whereClause = " WHERE courses.category_id = $cat_id ";
    }
    $pageTitle = "All Courses";
    $pageSubTitle = "Manage all courses on the platform.";
}

// 4. استعلام جلب الكورسات مع اسم التصنيف واسم المدرس
$sqlCourses = "SELECT courses.*, 
                      categories.name AS category_name,
                      users.name AS teacher_name 
               FROM courses 
               LEFT JOIN categories ON courses.category_id = categories.id 
               LEFT JOIN users ON courses.teacher_id = users.id 
               $whereClause 
               ORDER BY courses.id DESC";

$myCourses = $objCon->customQuery($sqlCourses);

// 5. استدعاء الهيدر
include "../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1"><?= $pageTitle ?></h2>
                <p class="text-muted mb-0"><?= $pageSubTitle ?></p>
            </div>

            <div class="d-flex gap-2">
                <!-- زر العودة للتصنيفات للطالب -->
                <?php if ($role === 'student'): ?>
                    <a href="../categories/index.php" class="btn btn-outline-secondary px-3 py-2 rounded-3 shadow-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Categories
                    </a>
                <?php endif; ?>

                <!-- زر الإضافة للمدرس والآدمن فقط -->
                <?php if ($role === 'teacher' || $role === 'admin'): ?>
                    <a href="add.php" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Add New Course
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Courses Grid / Cards -->
        <div class="row g-4">
            <?php if (!empty($myCourses)): ?>
                <?php foreach ($myCourses as $course): ?>
                    <?php
                    $imagePath = (!empty($course['image']) && file_exists("../upload/" . $course['image']))
                        ? "../upload/" . $course['image']
                        : "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&q=80";
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($course['title']) ?>"
                                style="height: 200px; object-fit: cover;">

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                                        <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                                    </span>
                                    <span class="fw-bold text-success fs-5">
                                        $<?= number_format($course['price'], 2) ?>
                                    </span>
                                </div>

                                <h5 class="card-title fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($course['title']) ?>
                                </h5>

                                <!-- 🔥 عرض اسم المدرس للطالب وللجميع -->
                                <div class="text-muted small mb-3">
                                    <i class="fa-solid fa-user-chalkboard me-1 text-secondary"></i>
                                    By: <span
                                        class="fw-semibold text-dark"><?= htmlspecialchars($course['teacher_name'] ?? 'Unknown Instructor') ?></span>
                                </div>

                                <p class="card-text text-muted small flex-grow-1">
                                    <?= htmlspecialchars(mb_strimwidth($course['description'] ?? '', 0, 100, "...")) ?>
                                </p>

                                <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                                    <a href="../lessons/index.php?course_id=<?= $course['id'] ?>"
                                        class="btn btn-outline-primary rounded-3 btn-sm">
                                        <i class="fa-solid fa-video me-1"></i> Lessons
                                    </a>

                                    <!-- أزرار التعديل تظهر للمدرس صاحب الكورس أو الآدمن فقط -->
                                    <?php if ($role === 'admin' || ($role === 'teacher' && $user_id == $course['teacher_id'])): ?>
                                        <div>
                                            <a href="../courses/edit.php?id=<?= $course['id'] ?>"
                                                class="btn btn-sm btn-light rounded-circle text-secondary" title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm p-5 rounded-4 bg-white">
                        <i class="fa-solid fa-folder-open text-muted fs-1 mb-3"></i>
                        <h4 class="fw-bold">No Courses Found</h4>
                        <p class="text-muted">
                            <?= $role === 'teacher' ? "You haven't added any courses to your account yet." : "There are no courses created in this category yet." ?>
                        </p>
                        <?php if ($role === 'teacher'): ?>
                            <div>
                                <a href="add.php" class="btn btn-primary px-4 py-2 rounded-3">Create First Course</a>
                            </div>
                        <?php else: ?>
                            <div>
                                <a href="../categories/index.php" class="btn btn-primary px-4 py-2 rounded-3">Browse
                                    Categories</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
include "../includes/footer.php";
?>