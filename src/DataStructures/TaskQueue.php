<?php

namespace procrastinot\DataStructures;

class TaskQueue
{
    private array $tasks = [];

    // Add a task to the queue
    public function enqueue(array $task): void
    {
        $this->tasks[] = $task;
    }

    // Return tasks from oldest to latest
    public function getOldestFirst(): array
    {
        $tasks = $this->tasks;
        $count = count($tasks);

        for ($i = 0; $i < $count - 1; $i++) {
            for ($j = 0; $j < $count - $i - 1; $j++) {

                $currentTime = strtotime($tasks[$j]['createdAt']);
                $nextTime = strtotime($tasks[$j + 1]['createdAt']);

                if ($currentTime > $nextTime) {
                    $temp = $tasks[$j];
                    $tasks[$j] = $tasks[$j + 1];
                    $tasks[$j + 1] = $temp;
                }
            }
        }

        return $tasks;
    }

    // Return tasks from latest to oldest
    public function getLatestFirst(): array
    {
        $tasks = $this->tasks;
        $count = count($tasks);

        for ($i = 0; $i < $count - 1; $i++) {
            for ($j = 0; $j < $count - $i - 1; $j++) {

                $currentTime = strtotime($tasks[$j]['createdAt']);
                $nextTime = strtotime($tasks[$j + 1]['createdAt']);

                if ($currentTime < $nextTime) {
                    $temp = $tasks[$j];
                    $tasks[$j] = $tasks[$j + 1];
                    $tasks[$j + 1] = $temp;
                }
            }
        }

        return $tasks;
    }
}
?>