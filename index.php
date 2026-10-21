<?php
use procrastinot\Algorithms\LinearSearch;
use procrastinot\Algorithms\BubbleSort;
use procrastinot\DataStructures\TaskBST;
use procrastinot\DataStructures\TaskQueue;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';




// Function to safely display data
function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Get the task to edit, if an Edit button was clicked
$editTask = null;

if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $taskID = (int) $_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM tasks WHERE taskID = ?"
    );

    $stmt->bind_param("i", $taskID);
    $stmt->execute();

    $result = $stmt->get_result();
    $editTask = $result->fetch_assoc();

    $stmt->close();
}

// Retrieve all tasks from the database
$taskResult = $conn->query("SELECT * FROM tasks");

$tasks = [];

if ($taskResult){
    while ($row = $taskResult -> fetch_assoc()){
        $tasks[] = $row;
    }
}

$taskBST = new TaskBST();

foreach ($tasks as $task) {
    $taskBST->insert($task);
}
// Check a task ID if one was provided in the URL
$foundTask = null;
$checkedID = false;

if (isset($_GET['check_id']) && ctype_digit($_GET['check_id'])) {
    $checkID = (int) $_GET['check_id'];
    $foundTask = $taskBST->search($checkID);
    $checkedID = true;
}

// Get the search keyword
$keyword = trim($_GET['search'] ?? '');

// Get the selected sorting option
$sort = $_GET['sort'] ?? '';

// Search tasks by title
$linearSearch = new LinearSearch();

if ($keyword !== '') {
    $tasks = $linearSearch->search($tasks, $keyword);
}

// Sort tasks by priority when requested
if ($sort === 'priority') {
    $bubbleSort = new BubbleSort();
    $tasks = $bubbleSort->sortByPriority($tasks);
}


$queueOrder = $_GET['queue_order'] ?? 'oldest';

$taskQueue = new TaskQueue();

foreach ($tasks as $task) {
    $taskQueue->enqueue($task);
}

if ($queueOrder === 'latest') {
    $tasks = $taskQueue->getLatestFirst();
} else {
    $tasks = $taskQueue->getOldestFirst();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ProcrastiNot | To-Do List</title>

    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Website Header -->
    <header>
        <h1>ProcrastiNot</h1>
        <p>Beat procrastination before it beats you</p>
    </header>

    <main class="container">

        <!-- Add or Edit Task Form -->
        <section class="task-form">

            <h2>
                <?= $editTask ? 'Edit Task' : 'Add a New Task' ?>
            </h2>

            <form method="POST" action="process.php">

                <!-- Tell process.php what action to perform -->
                <input
                    type="hidden"
                    name="action"
                    value="<?= $editTask ? 'edit' : 'add' ?>"
                >

                <?php if ($editTask): ?>
                    <input
                        type="hidden"
                        name="taskID"
                        value="<?= e($editTask['taskID']) ?>"
                    >
                <?php endif; ?>

                <!-- Task Title -->
                <label for="title">Task Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="255"
                    placeholder="What do you need to do?"
                    value="<?= e($editTask['title'] ?? '') ?>"
                    required
                >

                <!-- Task Description -->
                <label for="description">Description</label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Add some details (optional)"
                ><?= e($editTask['description'] ?? '') ?></textarea>

                <!-- Due Date -->
                <label for="due_date">Due Date</label>

                <input
                    type="date"
                    id="due_date"
                    name="due_date"
                    value="<?= e($editTask['due_date'] ?? '') ?>"
                >

                <!-- Priority -->
                <label for="priority">Priority</label>

                <select id="priority" name="priority">

                    <?php foreach (['Low', 'Medium', 'High'] as $level): ?>

                        <option
                            value="<?= e($level) ?>"
                            <?= ($editTask['priority'] ?? 'Medium') === $level
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($level) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <!-- Show Status only when editing -->
                <?php if ($editTask): ?>

                    <label for="status">Status</label>

                    <select id="status" name="status">

                        <?php foreach (['Pending', 'Completed'] as $state): ?>

                            <option
                                value="<?= e($state) ?>"
                                <?= $editTask['status'] === $state
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($state) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                <?php endif; ?>

                <!-- Submit Button -->
                <button type="submit" class="btn">
                    <?= $editTask ? 'Save Changes' : 'Add Task' ?>
                </button>

                <!-- Cancel Editing -->
                <?php if ($editTask): ?>

                    <a href="index.php" class="cancel-link">
                        Cancel
                    </a>

                <?php endif; ?>

            </form>

        </section>

        <!-- Task List -->
        <section class="task-list-section">

            <h2>My Tasks</h2>

            <form method="GET" action="index.php" class="search-form">
                <input
                    type="text"
                    name="search"
                    placeholder="Search tasks by title..."
                    value="<?= e($keyword) ?>"
                >

                <button type="submit" class="btn">Search</button>

                <a href="index.php" class="btn">Clear</a>

                <button
                    type="submit"
                    name="sort"
                    value="priority"
                    class="btn"
                >
                    Sort by Priority
                </button>
            </form>

            <form method="GET" action="index.php" class="search-form">

                <label for="queue_order">Task Queue Order</label>

                <!-- Preserve the current search keyword -->
                <input
                    type="hidden"
                    name="search"
                    value="<?= e($keyword) ?>"
                >

                <!-- Preserve the selected priority sort, if any -->
                <?php if ($sort === 'priority'): ?>
                    <input
                        type="hidden"
                        name="sort"
                        value="priority"
                    >
                <?php endif; ?>

                <select name="queue_order" id="queue_order">
                    <option value="oldest"
                        <?= $queueOrder === 'oldest' ? 'selected' : '' ?>>
                        Oldest to Latest
                    </option>

                    <option value="latest"
                        <?= $queueOrder === 'latest' ? 'selected' : '' ?>>
                        Latest to Oldest
                    </option>
                </select>

                <button type="submit" class="btn">Apply Queue Order</button>

            </form>

            <form method="GET" action="index.php" class="search-form">
                <label for="check_id">Check Task ID</label>

                <input
                    type="number"
                    id="check_id"
                    name="check_id"
                    min="1"
                    placeholder="Enter task ID"
                    required
                >

                <button type="submit" class="btn">Check ID</button>
            </form>

            <?php if ($checkedID): ?>

                <?php if ($foundTask !== null): ?>

                    <div class="task-card">
                        <h3>Task Found</h3>

                        <p>Task ID: <?= e($foundTask['taskID']) ?></p>
                        <p>Title: <?= e($foundTask['title']) ?></p>

                        <p>
                            Description:
                            <?= e($foundTask['description'] ?: 'No description') ?>
                        </p>

                        <p>Priority: <?= e($foundTask['priority']) ?></p>
                        <p>Status: <?= e($foundTask['status']) ?></p>

                        <a
                            class="btn edit-btn"
                            href="index.php?edit=<?= e($foundTask['taskID']) ?>"
                        >
                            Edit Task
                        </a>
                    </div>

                <?php else: ?>

                    <p class="empty-message">Task ID was not found.</p>

                <?php endif; ?>

            <?php endif; ?>

            <?php if (!empty($tasks)): ?>

                <div class="task-list">

                    <?php foreach ($tasks as $task): ?>

                        <article class="task-card <?= $task['status'] === 'Completed' ? 'completed' : '' ?>">

                            <div class="task-details">

                                <!-- Task Title -->
                                <h3><?= e($task['title']) ?></h3>

                                <!-- Task Description -->
                                <?php if (!empty($task['description'])): ?>

                                    <p>
                                        <?= nl2br(e($task['description'])) ?>
                                    </p>

                                <?php endif; ?>

                                <!-- Date and Time Created -->
                                <p class="task-created">
                                    Created:
                                    <?= e(date('M d, Y - h:i A', strtotime($task['createdAt']))) ?>
                                </p>

                                <!-- Task Priority -->
                                <p>
                                    Priority:

                                    <span class="priority <?= strtolower(e($task['priority'])) ?>">
                                        <?= e($task['priority']) ?>
                                    </span>
                                </p>

                                <!-- Due Date -->
                                <p>
                                    Due:
                                    <?= e($task['due_date'] ?: 'No deadline') ?>
                                </p>

                                <!-- Task Status -->
                                <p>
                                    Status: <?= e($task['status']) ?>
                                </p>

                            </div>

                            <!-- Task Action Buttons -->
                            <div class="task-actions">

                                <!-- Complete or Undo -->
                                <form method="POST" action="process.php">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="toggle"
                                    >

                                    <input
                                        type="hidden"
                                        name="taskID"
                                        value="<?= e($task['taskID']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="<?= $task['status'] === 'Completed'
                                            ? 'Pending'
                                            : 'Completed' ?>"
                                    >

                                    <button type="submit" class="btn">
                                        <?= $task['status'] === 'Completed'
                                            ? 'Undo'
                                            : 'Complete' ?>
                                    </button>

                                </form>

                                <!-- Edit Task -->
                                <a
                                    class="btn edit-btn"
                                    href="index.php?edit=<?= e($task['taskID']) ?>"
                                >
                                    Edit
                                </a>

                                <!-- Delete Task -->
                                <form
                                    method="POST"
                                    action="process.php"
                                    onsubmit="return confirm('Are you sure you want to delete this task?');"
                                >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="taskID"
                                        value="<?= e($task['taskID']) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn delete-btn"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

            <p class="empty-message">
                <?= $keyword !== ''
                    ? 'No tasks match your search.'
                    : 'No tasks yet. Add your first task above!' ?>
            </p>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
