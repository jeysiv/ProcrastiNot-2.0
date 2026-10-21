
<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$action = $_POST['action'] ?? '';

// ADD TASK
if ($action === 'add') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dueDate = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';

    if ($title !== '' &&
        in_array($priority, ['Low', 'Medium', 'High'], true)) {

        $dueDate = $dueDate !== '' ? $dueDate : null;

        $stmt = $conn->prepare(
            "INSERT INTO tasks
            (title, description, due_date, priority)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $title,
            $description,
            $dueDate,
            $priority
        );

        $stmt->execute();
        $stmt->close();
    }
}

// EDIT TASK
elseif ($action === 'edit') {
    $taskID = (int) ($_POST['taskID'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dueDate = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';
    $status = $_POST['status'] ?? 'Pending';

    if ($taskID > 0 && $title !== '' &&
        in_array($priority, ['Low', 'Medium', 'High'], true) &&
        in_array($status, ['Pending', 'Completed'], true)) {

        $dueDate = $dueDate !== '' ? $dueDate : null;

        $stmt = $conn->prepare(
            "UPDATE tasks
            SET title = ?, description = ?, due_date = ?,
                priority = ?, status = ?
            WHERE taskID = ?"
        );

        $stmt->bind_param(
            "sssssi",
            $title,
            $description,
            $dueDate,
            $priority,
            $status,
            $taskID
        );

        $stmt->execute();
        $stmt->close();
    }
}

// DELETE TASK
elseif ($action === 'delete') {
    $taskID = (int) ($_POST['taskID'] ?? 0);

    if ($taskID > 0) {
        $stmt = $conn->prepare(
            "DELETE FROM tasks WHERE taskID = ?"
        );

        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        $stmt->close();
    }
}

// TOGGLE TASK STATUS
elseif ($action === 'toggle') {
    $taskID = (int) ($_POST['taskID'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($taskID > 0 &&
        in_array($status, ['Pending', 'Completed'], true)) {

        $stmt = $conn->prepare(
            "UPDATE tasks SET status = ? WHERE taskID = ?"
        );

        $stmt->bind_param("si", $status, $taskID);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

header('Location: index.php');
exit;
