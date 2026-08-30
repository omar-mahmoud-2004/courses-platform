<?php
$host = 'localhost';
$db = 'courses-platform';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM lessons");
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_lessons = count($lessons);

$student_id = 1;
$progress_stmt = $pdo->prepare("SELECT COUNT(*) FROM progress WHERE student_id = ?");
$progress_stmt->execute([$student_id]);
$completed_lessons = $progress_stmt->fetchColumn();

$progress = $total_lessons > 0 ? ($completed_lessons / $total_lessons) * 100 : 0;
$progress = round($progress);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Course Progress Management</h2>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Overall Progress</h5>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                        <?php echo $progress; ?>% Completed (<?php echo $completed_lessons; ?> of <?php echo $total_lessons; ?>)
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lesson ID</th>
                            <th>Course ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lessons)): ?>
                            <?php foreach ($lessons as $index => $lesson): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td style="font-weight: 500;"><?php echo $lesson['id']; ?></td>
                                    <td><?php echo htmlspecialchars($lesson['course_id']); ?></td>
                                    <td>
                                        <a href="../lessons/edit.php?id=<?php echo $lesson['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="../lessons/delete.php?id=<?php echo $lesson['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No lessons found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>