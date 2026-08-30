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

$error = "";
$success = "";

// بنجيب بيانات الطالب
$student = $objCon->selectone("users", $student_id);

if (empty($student)) {
    die("Student not found.");
}

// ==========================================
// Update Profile
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // التحقق من البيانات
    if ($name === "" || $email === "") {

        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email.";
    } else {

        // بنشوف هل الإيميل مستخدم من حد تاني
        $checkEmail = $objCon->customQuery("
            SELECT id
            FROM users
            WHERE email = '$email'
            AND id != $student_id
            LIMIT 1
        ");

        if (!empty($checkEmail)) {

            $error = "This email is already used.";
        } else {

            // لو الطالب كتب Password جديدة
            if ($password !== "") {

                $data = [
                    "name" => $name,
                    "email" => $email,
                    "password" => $password
                ];
            } else {

                // لو مش عايز يغير الـ Password
                $data = [
                    "name" => $name,
                    "email" => $email
                ];
            }

            // تحديث بيانات الطالب
            if ($objCon->update($data, "users", $student_id)) {

                // تحديث بيانات الـ Session
                $_SESSION["name"] = $name;
                $_SESSION["email"] = $email;

                $success = "Profile updated successfully.";

                // نجيب البيانات الجديدة
                $student = $objCon->selectone("users", $student_id);
            } else {

                $error = "Failed to update profile. " . $objCon->getError();
            }
        }
    }
}

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
                ACCOUNT
            </span>

            <h2 class="fw-bold mt-2 mb-1">
                My Profile 👤
            </h2>

            <p class="text-muted mb-0">
                Manage your personal information and account settings.
            </p>

        </div>

        <div class="row justify-content-center">

            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm rounded-3">

                    <!-- Profile Header -->
                    <div class="card-body p-4 p-lg-5">

                        <div class="text-center mb-4">

                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mx-auto mb-3"
                                style="width:90px;height:90px;font-size:2.5rem;">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <h4 class="fw-bold mb-1">
                                <?= htmlspecialchars($student["name"]) ?>
                            </h4>

                            <p class="text-muted mb-0">
                                Student Account
                            </p>

                        </div>

                        <!-- Success -->
                        <?php if ($success !== ""): ?>

                            <div class="alert alert-success">

                                <i class="fa-solid fa-circle-check me-2"></i>

                                <?= htmlspecialchars($success) ?>

                            </div>

                        <?php endif; ?>

                        <!-- Error -->
                        <?php if ($error !== ""): ?>

                            <div class="alert alert-danger">

                                <i class="fa-solid fa-circle-exclamation me-2"></i>

                                <?= htmlspecialchars($error) ?>

                            </div>

                        <?php endif; ?>

                        <!-- Profile Form -->
                        <form method="POST">

                            <!-- Name -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Full Name
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-user text-muted"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($student["name"]) ?>"
                                        required>

                                </div>

                            </div>

                            <!-- Email -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Email
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-envelope text-muted"></i>
                                    </span>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="<?= htmlspecialchars($student["email"]) ?>"
                                        required>

                                </div>

                            </div>

                            <!-- Account Type -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Account Type
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-user-graduate text-muted"></i>
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        value="Student"
                                        readonly>

                                </div>

                            </div>

                            <!-- Password -->
                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    New Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-lock text-muted"></i>
                                    </span>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Leave empty to keep current password">

                                </div>

                                <div class="form-text">
                                    Leave this field empty if you don't want to change your password.
                                </div>

                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary px-4 rounded-3">

                                    <i class="fa-solid fa-floppy-disk me-2"></i>
                                    Update Profile

                                </button>

                                <a
                                    href="dashboard.php"
                                    class="btn btn-outline-secondary px-4 rounded-3">

                                    Back to Dashboard

                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php
// بنجيب الـ Footer
include "../includes/footer.php";
?>