
<?php

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
require_once "../connect.php";
$objCon = new connect();

// Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = (int) $_SESSION['user_id'];

// Get Course ID
$course_id = (int) ($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    die("Course not found.");
}


// ==========================================
// Check if student is enrolled in the course
// ==========================================

$enrollment = $objCon->customQuery("
    SELECT id
    FROM enrollments
    WHERE student_id = $student_id
    AND course_id = $course_id
    LIMIT 1
");

if (empty($enrollment)) {
    die("You are not enrolled in this course.");
}


// ==========================================
// Get Course
// ==========================================

$courseData = $objCon->customQuery("
    SELECT id, title
    FROM courses
    WHERE id = $course_id
    LIMIT 1
");

if (empty($courseData)) {
    die("Course not found.");
}

$course = $courseData[0];


// ==========================================
// Get Quiz Questions
// ==========================================

$questions = $objCon->customQuery("
    SELECT
        id,
        question,
        answer,
        correct_answer
    FROM quizzes
    WHERE course_id = $course_id
    ORDER BY id ASC
");


// ==========================================
// Submit Quiz
// ==========================================

$score = null;
$totalQuestions = count($questions);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($questions)) {

    $score = 0;

    foreach ($questions as $q) {

        $question_id = (int) $q['id'];

        $userAnswer = $_POST['answer'][$question_id] ?? '';

        if (
            trim(strtolower($userAnswer))
            ===
            trim(strtolower($q['correct_answer']))
        ) {
            $score++;
        }
    }

    $percentage = $totalQuestions > 0
        ? round(($score / $totalQuestions) * 100)
        : 0;
}


// ==========================================
// Header
// ==========================================

include "../includes/header.php";
?>

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


<main class="main-content flex-grow-1 w-100">

    <div class="container-fluid py-4 px-4 px-lg-5">

        <!-- Back -->
        <div class="mb-4">

            <a href="course.php?id=<?= $course_id ?>"
                class="text-decoration-none text-muted">

                <i class="fa-solid fa-arrow-left me-2"></i>

                Back to Course

            </a>

        </div>


        <!-- Quiz Header -->

        <div class="card border-0 shadow-sm rounded-3 mb-4">

            <div class="card-body p-4 p-lg-5">

                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3">

                    QUIZ

                </span>

                <h2 class="fw-bold mb-2">

                    <?= htmlspecialchars($course['title']) ?>

                </h2>

                <p class="text-muted mb-0">

                    Test your knowledge of this course.

                </p>

            </div>

        </div>


        <?php if ($score !== null): ?>

            <!-- Result -->

            <div class="card border-0 shadow-sm rounded-3 mb-4">

                <div class="card-body text-center p-5">

                    <i class="fa-solid fa-circle-check text-success fa-4x mb-3"></i>

                    <h3 class="fw-bold">

                        Quiz Completed 🎉

                    </h3>

                    <p class="text-muted">

                        Your Score

                    </p>

                    <h1 class="fw-bold text-primary">

                        <?= $score ?> / <?= $totalQuestions ?>

                    </h1>

                    <h4 class="fw-bold">

                        <?= $percentage ?>%

                    </h4>

                    <a href="quiz.php?course_id=<?= $course_id ?>"
                        class="btn btn-primary rounded-3 px-4 mt-3">

                        Try Again

                    </a>

                </div>

            </div>


        <?php elseif (empty($questions)): ?>

            <!-- No Questions -->

            <div class="card border-0 shadow-sm rounded-3">

                <div class="card-body text-center py-5">

                    <i class="fa-solid fa-clipboard-question fa-4x text-muted mb-3"></i>

                    <h4 class="fw-bold">

                        No Quiz Questions Yet

                    </h4>

                    <p class="text-muted">

                        There are no questions available for this course yet.

                    </p>

                </div>

            </div>


        <?php else: ?>

            <!-- Quiz Form -->

            <form method="POST">

                <?php foreach ($questions as $index => $q): ?>

                    <div class="card border-0 shadow-sm rounded-3 mb-4">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-4">

                                Question <?= $index + 1 ?>

                            </h5>


                            <p class="fs-5 mb-4">

                                <?= htmlspecialchars($q['question']) ?>

                            </p>


                            <!-- Answer -->

                            <div class="mb-3">

                                <input
                                    type="text"
                                    name="answer[<?= (int)$q['id'] ?>]"
                                    class="form-control form-control-lg"
                                    placeholder="Write your answer..."
                                    required>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>


                <!-- Submit -->

                <div class="text-center">

                    <button type="submit"
                        class="btn btn-success btn-lg rounded-3 px-5">

                        <i class="fa-solid fa-check me-2"></i>

                        Submit Quiz

                    </button>

                </div>

            </form>

        <?php endif; ?>

    </div>

</main>


<?php

include "../includes/footer.php";

?>

