<?php
// 1. فحص الجلسة وصلاحية الآدمن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /courses-platform/auth/login.php");
    exit();
}

// 2. الاتصال بقاعدة البيانات عبر الكلاس
include "../connect.php";
$objcon = new connect();

$success_msg = "";
$error_msg = "";

// 3. معالجة تعديل الصلاحية باستخدام دالة update()
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $target_user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'] ?? '';

    if (in_array($new_role, ['student', 'teacher', 'admin'])) {
        if ($objcon->update(['role' => $new_role], "users", $target_user_id)) {
            $success_msg = "User role updated successfully.";
        } else {
            $error_msg = "Failed to update user role: " . $objcon->getError();
        }
    }
}

// 4. معالجة حذف المستخدم باستخدام دالة delete()
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);

    // حماية: منع الآدمن من حذف حسابه الشخصي المسجل به حالياً
    if ($delete_id === intval($_SESSION['user_id'])) {
        $error_msg = "You cannot delete your own admin account!";
    } else {
        if ($objcon->delete("users", $delete_id)) {
            $success_msg = "User deleted successfully.";
        } else {
            $error_msg = "Failed to delete user: " . $objcon->getError();
        }
    }
}

// 5. جلب كافة المستخدمين
$users = $objcon->select("users") ?? [];

// 6. استدعاء الهيدر الموحد
include "../includes/header.php";
?>

<div class="container py-5">

    <!-- شريط التنقل والعنوان -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <small class="text-primary fw-bold text-uppercase">Admin Panel</small>
            <h1 class="fw-bold mt-2 mb-0">Users Management</h1>
            <p class="text-secondary fs-6 mb-0">Manage roles, permissions, and accounts across the platform.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="users.php" class="btn btn-primary active">
                <i class="bi bi-people me-1"></i> Users
            </a>
            <a href="courses.php" class="btn btn-outline-primary">
                <i class="bi bi-book me-1"></i> Courses
            </a>
            <a href="categories.php" class="btn btn-outline-primary">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            <a href="reviews.php" class="btn btn-outline-primary">
                <i class="bi bi-star me-1"></i> Reviews
            </a>
        </div>
    </div>

    <!-- رسائل النجاح أو الخطأ -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- بطاقة جدول المستخدمين -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        
        <!-- شريط البحث والفلترة السريعة -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-people-fill text-primary me-2"></i>All Users (<?= count($users) ?>)
            </h5>
            <div class="col-md-4">
                <input type="text" id="userSearchInput" class="form-control form-control-sm" placeholder="Search by name or email...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Change Role</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= htmlspecialchars($user['id'] ?? '') ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-person text-secondary fs-5"></i>
                                        </div>
                                        <span class="fw-semibold user-name"><?= htmlspecialchars($user['name'] ?? '') ?></span>
                                    </div>
                                </td>
                                <td class="text-muted user-email"><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                <td>
                                    <span class="badge <?= ($user['role'] ?? '') === 'admin' ? 'bg-danger' : (($user['role'] ?? '') === 'teacher' ? 'bg-success' : 'bg-primary') ?>">
                                        <?= ucfirst($user['role'] ?? 'student') ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- فورم تغيير الـ Role مباشرة -->
                                    <form method="POST" action="users.php" class="d-flex align-items-center gap-2 m-0">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="role" class="form-select form-select-sm" style="width: 120px;">
                                            <option value="student" <?= ($user['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                                            <option value="teacher" <?= ($user['role'] === 'teacher') ? 'selected' : '' ?>>Teacher</option>
                                            <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <button type="submit" name="update_role" class="btn btn-sm btn-outline-primary" title="Update Role">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <?php if (intval($user['id']) !== intval($_SESSION['user_id'])): ?>
                                        <a href="users.php?action=delete&id=<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this user? All their related data will be removed.');"
                                           title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border">Active You</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- البحث الفوري عبر JavaScript -->
<script>
document.getElementById('userSearchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#usersTable tbody tr');

    rows.forEach(row => {
        let name = row.querySelector('.user-name')?.innerText.toLowerCase() || '';
        let email = row.querySelector('.user-email')?.innerText.toLowerCase() || '';
        if (name.includes(filter) || email.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php
include "../includes/footer.php";
?>