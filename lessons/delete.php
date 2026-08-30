<?php
$host = 'localhost';
$db = 'courses-platform';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


$id = $_GET['id'] ?? null;
$course_id = $_GET['course_id'] ?? 0;


if ($id) {
    $sql = "DELETE FROM lessons WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: index.php?course_id=" . $course_id);
exit();
?>