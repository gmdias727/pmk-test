<?php

function reorder_by_magnitude(array $numbers): array {
    $length = count($numbers);

    for($i = 0; $i < $length; $i++) {
        $minIndex = $i

        for($j = $i + 1; $j < $length; $j++) {
            if(abs($numbers[$j]) < abs($numbers[$minIndex])) {
                $minIndex = $j;
            }
        }

        if ($minIndex != $i) {
            $swap = $numbers[$i];
            $numbers[$i] = $numbers[$minIndex];
            $numbers[$minIndex] = $swap
        }
    }
    return $numbers;
}
