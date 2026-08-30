<?php
require_once "../connect.php";
$objCon = new connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$name = "";
$email = "";
$role = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $role = $_POST["role"] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        // فحص الإيميل عبر customQuery
        $check_sql = "SELECT id FROM users WHERE email = '$email'";
        $check_result = $objCon->customQuery($check_sql);

        if (is_array($check_result) && count($check_result) > 0) {
            $error = "Email already exists.";
        } else {
            // حفظ كلمة المرور مباشرة بدون تشفير
            $insert_sql = "INSERT INTO users (name, email, password, role) 
                          VALUES ('$name', '$email', '$password', '$role')";

            $objCon->customQuery($insert_sql);

            header("Location: login.php?registered=success");
            exit;
        }
    }
}
?>

<?php require_once "../includes/header.php"; ?>

<link rel="stylesheet" href="../assets/css/style_Login.css">

<section class="auth-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card card border-0 shadow-sm p-4 rounded-3 bg-white">

                    <div class="auth-icon text-center mb-3 text-primary fs-1">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>

                    <h2 class="text-center fw-bold mb-1">Create Account</h2>
                    <p class="auth-subtitle text-center text-muted mb-4">
                        Join our learning platform today
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 px-3 mb-3 small">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="name" class="form-control border-start-0 ps-0 shadow-none"
                                    placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0 shadow-none"
                                    placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password"
                                    class="form-control border-start-0 ps-0 shadow-none" placeholder="Create a password"
                                    required>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Account Type</label>
                            <select name="role" class="form-select shadow-none" required>
                                <option value="">Select account type</option>
                                <option value="student" <?php echo ($role === 'student') ? 'selected' : ''; ?>>Student
                                </option>
                                <option value="teacher" <?php echo ($role === 'teacher') ? 'selected' : ''; ?>>Teacher
                                </option>
                                <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                            Create Account <i class="bi bi-arrow-right ms-1"></i>
                        </button>

                    </form>

                    <div class="auth-bottom text-center small text-muted">
                        Already have an account?
                        <a href="login.php" class="text-primary text-decoration-none fw-semibold">Login</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>