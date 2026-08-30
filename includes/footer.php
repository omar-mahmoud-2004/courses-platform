</div>

<footer class="footer">

    <div class="container">

        <div class="row g-4">

            <div class="col-lg-5">

                <div class="footer-logo">

                    <i class="bi bi-mortarboard-fill"></i>

                    Course<span>Hub</span>

                </div>

                <p class="footer-text">

                    Learn new skills, improve your knowledge,
                    and build your future with online courses.

                </p>

            </div>

            <div class="col-md-3">

                <h5>Platform</h5>

                <a href="/courses-platform/index.php">
                    Home
                </a>

                <a href="/courses-platform/courses/index.php">
                    Courses
                </a>

            </div>

            <div class="col-md-4">

                <h5>Account</h5>

                <?php if (isset($_SESSION["user_id"])): ?>

                    <a href="/courses-platform/auth/logout.php">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="/courses-platform/auth/login.php">
                        Login
                    </a>

                    <a href="/courses-platform/auth/register.php">
                        Register
                    </a>

                <?php endif; ?>

            </div>

        </div>

        <hr>

        <div class="copyright">

            © 2026 CourseHub. All rights reserved.

        </div>

    </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="/courses-platform/assets/js/script.js"></script>

</body>

</html>