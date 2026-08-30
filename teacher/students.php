<?php
// 1. استدعاء ملف الاتصال وقاعدة البيانات
include "../connect.php";
$objCon = new connect();

// 2. استدعاء الـ Header والـ Navbar
include "../includes/header.php";

// 3. استقبال قيم الفلترة والبحث
$selected_course = isset($_GET['course_id']) ? $_GET['course_id'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// 4. جلب جميع الكورسات للـ Dropdown Filter
$courses_list = $objCon->select("courses");
if (!is_array($courses_list)) {
    $courses_list = [];
}

// 5. جلب المستخدمين من جدول users
$all_users = $objCon->select("users");
if (!is_array($all_users)) {
    $all_users = [];
}

// 6. فلترة المستخدمين للتركيز على الطلاب (في حال وجود عمود role أو جلب الجميع)
$students = array_filter($all_users, function ($user) {
    if (isset($user['role'])) {
        return strtolower($user['role']) === 'student';
    }
    return true;
});

// 7. تطبيق الفلترة بالبحث والكورس في PHP
if (count($students) > 0) {

    // فلترة حسب الكورس
    if ($selected_course !== 'all' && (int)$selected_course > 0) {
        $students = array_filter($students, function ($student) use ($selected_course) {
            return isset($student['course_id']) && $student['course_id'] == $selected_course;
        });
    }

    // فلترة حسب اسم الطالب أو الإيميل
    if (!empty($search)) {
        $students = array_filter($students, function ($student) use ($search) {
            $nameMatch = isset($student['name']) && stripos($student['name'], $search) !== false;
            $emailMatch = isset($student['email']) && stripos($student['email'], $search) !== false;
            return $nameMatch || $emailMatch;
        });
    }
}

// إنشاء مصفوفة لمطابقة اسم الكورس برقم id لتسهيل العرض
$course_names = [];
foreach ($courses_list as $c) {
    $course_names[$c['id']] = $c['title'];
}

// حساب إجمالي عدد الطلاب بعد الفلترة
$total_students = count($students);
?>

<!-- main wrapper لإلغاء المسافة من أعلى وتنسيق المحتوى -->
<main class="main-content flex-grow-1 w-100 mt-0 pt-0">
    <div class="container-fluid pt-2 pb-4 px-4 px-lg-5">

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-2">
            <div>
                <h2 class="fw-bold mb-1">Enrolled Students</h2>
                <p class="text-muted mb-0">Track and monitor student progress across all your courses.</p>
            </div>
            <div>
                <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-users me-1"></i> Total Students: <?= number_format($total_students) ?>
                </span>
            </div>
        </div>

        <!-- Filter & Search Bar Card -->
        <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-white">
            <form action="" method="GET" class="row g-3 align-items-center">

                <!-- Search Input -->
                <div class="col-md-5 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0 ps-0 shadow-none"
                            placeholder="Search student by name or email...">
                    </div>
                </div>

                <!-- Course Filter -->
                <div class="col-md-5 col-lg-4">
                    <select name="course_id" class="form-select shadow-none" onchange="this.form.submit()">
                        <option value="all" <?= $selected_course == 'all' ? 'selected' : ''; ?>>All Courses</option>
                        <?php foreach ($courses_list as $c) { ?>
                            <option value="<?= $c['id'] ?>" <?= $selected_course == $c['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($c['title']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Search Button -->
                <div class="col-md-2 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                </div>

            </form>
        </div>

        <!-- Students Table Card -->
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Enrolled Course</th>
                            <th>Enrolled Date</th>
                            <th>Progress</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($students) > 0) { ?>
                            <?php foreach ($students as $student) { 
                                // جلب اسم الكورس أو تحديد General Student إذا لم يكن مرتبط بكورس
                                $course_id = $student['course_id'] ?? null;
                                $course_title = ($course_id && isset($course_names[$course_id])) 
                                    ? $course_names[$course_id] 
                                    : 'General Student';

                                // حساب التقدّم وتاريخ التسجيل
                                $progress = isset($student['progress']) ? (int)$student['progress'] : 0;
                                $enrolled_date = isset($student['created_at']) && !empty($student['created_at']) 
                                    ? date('M d, Y', strtotime($student['created_at'])) 
                                    : 'N/A';

                                // تحديد لون شريط التقدّم
                                $progressBarBg = 'bg-primary';
                                if ($progress >= 100) {
                                    $progressBarBg = 'bg-success';
                                } elseif ($progress < 30) {
                                    $progressBarBg = 'bg-warning';
                                }

                                // تحديد شارة الحالة (Status Badge)
                                $statusText = 'Active';
                                $statusBadgeClass = 'bg-success-subtle text-success border-success-subtle';
                                
                                if ($progress >= 100) {
                                    $statusText = 'Completed';
                                    $statusBadgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
                                } elseif (isset($student['status']) && $student['status'] == 'inactive') {
                                    $statusText = 'Inactive';
                                    $statusBadgeClass = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($student['name'] ?? ($student['username'] ?? 'Student')) ?>&background=random"
                                                class="rounded-circle me-3" width="40" height="40" alt="Student Avatar">
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    <?= htmlspecialchars($student['name'] ?? ($student['username'] ?? 'N/A')) ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars($student['email'] ?? '') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            <?= htmlspecialchars($course_title) ?>
                                        </span>
                                    </td>
                                    <td><span class="small text-muted"><?= $enrolled_date ?></span></td>
                                    <td style="width: 200px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar <?= $progressBarBg ?>" role="progressbar" 
                                                    style="width: <?= $progress ?>%;"
                                                    aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="small fw-semibold text-dark"><?= $progress ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge <?= $statusBadgeClass ?> border px-3 py-1 rounded-pill small fw-normal">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-user-slash fs-3 mb-2 d-block"></i>
                                    No students found matching your criteria.
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div> <!-- إغلاق Container المحتوى الرئيسي -->
</main>

<?php
// 8. استدعاء الـ Footer
include "../includes/footer.php";
?>
