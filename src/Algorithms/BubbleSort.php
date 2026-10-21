<?php
namespace procrastinot\Algorithms;

class BubbleSort{
    public function sortByPriority(array $tasks): array{
        $count = count($tasks);

        for ($i = 0; $i < $count - 1; $i++){
            for ($j = 0; $j < $count - $i - 1; $j++){
                
                $priorityOrder = [
                    'High' => 1,
                    'Medium' => 2,
                    'Low' => 3,
                ];

                $currentPriority = $priorityOrder[$tasks[$j]['priority']];
                $nextPriority = $priorityOrder[$tasks[$j + 1]['priority']];

                if($currentPriority > $nextPriority){
                    $temp = $tasks[$j];
                    $tasks[$j] = $tasks[$j +1];
                    $tasks[$j+1] = $temp;
                }
            }
        }
        return $tasks;
    }
}
?>