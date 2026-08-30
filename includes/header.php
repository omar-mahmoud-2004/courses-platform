<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Courses Platform</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="..\assets\cs\style_Login.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container">

            <a class="navbar-brand" href="/courses-platform/index.php">
                <span class="logo-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                Course<span>Hub</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav mx-auto">

                    <li class="nav-item">





                        <a class="nav-link" href="/courses-platform/index.php">Home</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <?php if ($_SESSION['role'] == 'student'): ?>

                            <li class="nav-item">
                                <a class="nav-link" href="/courses-platform/student/dashboard.php">
                                    Dashboard
                                </a>
                            </li>

                            <!-- إضافة رابط الكورسات للطالب -->
                            <li class="nav-item">
                                <a class="nav-link" href="..\courses\index.php">
                                    Courses
                                </a>
                            </li>


                        <?php elseif ($_SESSION['role'] == 'teacher'): ?>

                            <a class="nav-link" href="/courses-platform/teacher/dashboard.php">Dashboard</a>
                            <a class="nav-link" href="/courses-platform/courses/index.php">Courses</a>

                            <a class="nav-link" href="/courses-platform/teacher/students.php">Students</a>



                        <?php elseif ($_SESSION['role'] == 'admin'): ?>

                            <li class="nav-item">
                                <a class="nav-link" href="/courses-platform/admin/dashboard.php">
                                    Dashboard
                                </a>
                            </li>

                        <?php endif; ?>

                    <?php endif; ?>

                </ul>

                <div class="navbar-actions">

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <span class="welcome-user">
                            Hi, <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </span>

                        <a href="/courses-platform/auth/logout.php" class="btn-primary-custom">
                            Logout
                        </a>

                    <?php else: ?>

                        <a href="/courses-platform/auth/login.php" class="login-link">
                            Login
                        </a>

                        <a href="/courses-platform/auth/register.php" class="btn-primary-custom">
                            Register
                        </a>

                    <?php endif; ?>

                </div>

            </div>
        </div>
    </nav>

    <div class="page-content">