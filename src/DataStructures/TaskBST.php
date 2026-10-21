<?php

namespace procrastinot\DataStructures;

class TaskBST
{
    private ?array $root = null;

    public function insert(array $task): void
    {
        $newNode = [
            'task' => $task,
            'left' => null,
            'right' => null
        ];

        if ($this->root === null) {
            $this->root = $newNode;
            return;
        }

        $current = &$this->root;
        $taskID = (int) $task['taskID'];

        while (true) {
            $currentID = (int) $current['task']['taskID'];

            if ($taskID < $currentID) {
                if ($current['left'] === null) {
                    $current['left'] = $newNode;
                    return;
                }

                $current = &$current['left'];
            } elseif ($taskID > $currentID) {
                if ($current['right'] === null) {
                    $current['right'] = $newNode;
                    return;
                }

                $current = &$current['right'];
            } else {
                return;
            }
        }
    }

    public function search(int $taskID): ?array
    {
        $current = $this->root;

        while ($current !== null) {
            $currentID = (int) $current['task']['taskID'];

            if ($taskID === $currentID) {
                return $current['task'];
            }

            if ($taskID < $currentID) {
                $current = $current['left'];
            } else {
                $current = $current['right'];
            }
        }

        return null;
    }
}
?>