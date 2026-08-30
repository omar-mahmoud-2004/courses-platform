<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../connect.php";
$objCon = new connect();


if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$email = "";
$error = "";
$success_msg = "";

if (isset($_GET['registered']) && $_GET['registered'] === 'success') {
    $success_msg = "Account created successfully! Please login below.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";
    } else {

        $sql = "SELECT * FROM users WHERE email = '$email'";
        $users = $objCon->customQuery($sql);

        if (is_array($users) && count($users) === 1) {
            $user = $users[0];

            // مقارنة النص الصريح مباشرة بدون تشفير
            if ($password === $user["password"]) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];
                
                // أجبِر السيرفر على حفظ الجلسة وقفلها قبل الانتقال 
                session_write_close();

                if ($user["role"] === "student") {
                    header("Location: ../student/dashboard.php");
                } elseif ($user["role"] === "teacher") {
                    header("Location: ../teacher/dashboard.php");
                } elseif ($user["role"] === "admin") {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit;

            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
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
                        <i class="bi bi-box-arrow-in-right"></i>
                    </div>

                    <h2 class="text-center fw-bold mb-1">Welcome Back</h2>
                    <p class="auth-subtitle text-center text-muted mb-4">
                        Login to continue learning
                    </p>

                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success py-2 px-3 mb-3 small">
                            <?php echo htmlspecialchars($success_msg); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 px-3 mb-3 small">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">

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

                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password"
                                    class="form-control border-start-0 ps-0 shadow-none"
                                    placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                            Login <i class="bi bi-arrow-right ms-1"></i>
                        </button>

                    </form>

                    <div class="auth-bottom text-center small text-muted">
                        Don't have an account?
                        <a href="register.php" class="text-primary text-decoration-none fw-semibold">Create Account</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>