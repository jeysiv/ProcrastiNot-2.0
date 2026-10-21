<?php

namespace procrastinot\Algorithms;

class LinearSearch{
    public function search(array $tasks, string $keyword): array{
        $matches = [];

        foreach($tasks as $task){
            if (stripos($task['title'], $keyword) !== false){
                $matches[] = $task;
            }
        }

        return $matches;
    }
}



?>