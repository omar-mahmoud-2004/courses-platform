<?php
session_start();

$host = 'localhost';
$db = 'courses-platform';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['lesson_id'])) {
    $student_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    $lesson_id = (int) $_POST['lesson_id'];

    $check = mysqli_query($conn, "SELECT * FROM progress WHERE student_id = '$student_id' AND lesson_id = '$lesson_id'");
    
    if (mysqli_num_rows($check) == 0) {
        $query = "INSERT INTO progress (student_id, lesson_id) VALUES ('$student_id', '$lesson_id')";
        mysqli_query($conn, $query);
    }
    
    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>