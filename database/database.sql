CREATE DATABASE IF NOT EXISTS procrastinot;
USE procrastinot;

CREATE TABLE tasks (
    taskID INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    due_date DATE NULL,
    priority ENUM ('Low','Medium','High') NOT NULL DEFAULT 'MEDIUM',
    status ENUM('Pending', 'Completed') NOT NULL DEFAULT 'PENDING',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deletedAt DATETIME NULL DEFAULT NULL
);
