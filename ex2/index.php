<?php

function reorder(array $numbers): array {
    $length = count($numbers);

    for($i = 0; $i < $length; $i++) {
        $minIndex = $i;

        for($j = $i + 1; $j < $length; $j++) {
            if(abs($numbers[$j]) < abs($numbers[$minIndex])) {
                $minIndex = $j;
            }
        }

        if ($minIndex != $i) {
            $swap = $numbers[$i];
            $numbers[$i] = $numbers[$minIndex];
            $numbers[$minIndex] = $swap;
        }
    }
    return $numbers;
}


print_r(reorder([50,1,5,65,35,22,100,300,250]));
