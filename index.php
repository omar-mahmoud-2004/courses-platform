<?php

require_once "connect.php";


?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php require_once "includes/header.php"; ?>
<link rel="stylesheet" href="assets\cs\style_Login.css">
</section>
<section class="hero-section">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <div class="hero-content">

                    <span class="hero-badge">
                        <i class="bi bi-stars"></i>
                        Online Learning Platform
                    </span>

                    <h1>
                        Learn Skills.
                        <span>Grow Your Future.</span>
                    </h1>

                    <p>
                        Discover online courses, learn from experienced teachers,
                        and improve your skills step by step.
                    </p>

                    <div class="hero-buttons">

                        <a href="auth/register.php" class="hero-primary-btn">

                            Get Started
                            <i class="bi bi-arrow-right"></i>

                        </a>

                        <a href="courses/index.php" class="hero-secondary-btn">

                            Browse Courses

                        </a>

                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="hero-visual">

                    <div class="hero-circle"></div>

                    <div class="learning-card">

                        <div class="learning-icon">
                            <i class="bi bi-play-fill"></i>
                        </div>

                        <div>

                            <strong>
                                Start Learning
                            </strong>

                            <small>
                                Learn at your own pace
                            </small>

                        </div>

                    </div>

                    <div class="floating-card">

                        <i class="bi bi-check-circle-fill"></i>

                        <div>

                            <strong>
                                Keep Learning
                            </strong>

                            <small>
                                Track your progress
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="features-section">

    <div class="container">

        <div class="row g-4">

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-laptop"></i>
                    </div>

                    <h4>
                        Learn Online
                    </h4>

                    <p>
                        Access your courses and lessons
                        from anywhere.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-person-video3"></i>
                    </div>

                    <h4>
                        Expert Teachers
                    </h4>

                    <p>
                        Learn from teachers who share
                        their knowledge and experience.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>

                    <h4>
                        Track Progress
                    </h4>

                    <p>
                        Follow your learning progress
                        as you complete your lessons.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="courses-section">

    <div class="container">

        <div class="section-heading">

            <div>

                <span>
                    Start Learning
                </span>

                <h2>
                    Popular Courses
                </h2>

            </div>

            <a href="courses/index.php">
                View All
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>

        <div class="row g-4">

            <div class="col-lg-4 col-md-6">

                <div class="course-card">

                    <div class="course-image">

                        <div class="course-placeholder">
                            <i class="bi bi-code-slash"></i>
                        </div>

                    </div>

                    <div class="course-body">

                        <span class="course-category">
                            Programming
                        </span>

                        <h4>
                            Web Development
                        </h4>

                        <p>
                            Learn the basics of creating
                            modern websites.
                        </p>

                        <div class="course-info">

                            <span>
                                <i class="bi bi-play-circle"></i>
                                Online Course
                            </span>

                            <span>
                                <i class="bi bi-star-fill"></i>
                                4.8
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 col-md-6">

                <div class="course-card">

                    <div class="course-image">

                        <div class="course-placeholder">
                            <i class="bi bi-database"></i>
                        </div>

                    </div>

                    <div class="course-body">

                        <span class="course-category">
                            Database
                        </span>

                        <h4>
                            SQL Database
                        </h4>

                        <p>
                            Understand databases and
                            write SQL queries.
                        </p>

                        <div class="course-info">

                            <span>
                                <i class="bi bi-play-circle"></i>
                                Online Course
                            </span>

                            <span>
                                <i class="bi bi-star-fill"></i>
                                4.9
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 col-md-6">

                <div class="course-card">

                    <div class="course-image">

                        <div class="course-placeholder">
                            <i class="bi bi-braces"></i>
                        </div>

                    </div>

                    <div class="course-body">

                        <span class="course-category">
                            Programming
                        </span>

                        <h4>
                            C# Programming
                        </h4>

                        <p>
                            Build your programming skills
                            with C#.
                        </p>

                        <div class="course-info">

                            <span>
                                <i class="bi bi-play-circle"></i>
                                Online Course
                            </span>

                            <span>
                                <i class="bi bi-star-fill"></i>
                                4.8
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="cta-section">

    <div class="container">

        <div class="cta-box">

            <div>

                <span>
                    Start today
                </span>

                <h2>
                    Ready to Start Learning?
                </h2>

                <p>
                    Create your account and explore
                    the available courses.
                </p>

            </div>

            <a href="auth/register.php" class="cta-button">

                Create Account
                <i class="bi bi-arrow-right"></i>

            </a>

        </div>

    </div>

</section>
<?php endif; ?>

<?php require_once "includes/footer.php"; ?>