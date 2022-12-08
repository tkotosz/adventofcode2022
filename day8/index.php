<?php

function take_until(array $items, callable $exitCondition): array
{
    $takenItems = [];

    foreach ($items as $item) {
        $takenItems[] = $item;

        if ($exitCondition($item)) {
            break;
        }
    }

    return $takenItems;
}

$inputFilePath = __DIR__ . '/input';
$input = array_map('str_split', explode("\n", file_get_contents($inputFilePath)));
$width = count($input[0]);
$height = count($input);

$visibleFromOutsideTreesCount = 0;
$maxScenicScore = 0;
for($i = 0; $i < $height; $i++) {
    $treesInRow = $input[$i];
    for ($j = 0; $j < $width; $j++) {
        $treesInColumn = array_column($input, $j);
        $treesLeft = array_slice($treesInRow, 0, $j);
        $treesRight = array_slice($treesInRow, $j+1);
        $treesUp = array_slice($treesInColumn, 0, $i);
        $treesDown = array_slice($treesInColumn, $i+1);
        $currentTreeSize = $input[$i][$j];
        $largerTreeFoundCheck = fn ($treeSize) => $treeSize >= $currentTreeSize;

        $visibleFromOutsideTreesCount += (int)(
            (empty($treesRight) || empty($treesLeft) || empty($treesUp) || empty($treesDown)) || // on the edge
            (max($treesLeft) < $currentTreeSize) || // visible from left
            (max($treesRight) < $currentTreeSize) || // visible from right
            (max($treesUp) < $currentTreeSize) || // visible from top
            (max($treesDown) < $currentTreeSize) // visible from bottom
        );

        $maxScenicScore = max(
            $maxScenicScore,
            (
                count(take_until(array_reverse($treesLeft), $largerTreeFoundCheck)) * // number of visible trees left
                count(take_until($treesRight, $largerTreeFoundCheck)) * // number of visible trees right
                count(take_until(array_reverse($treesUp), $largerTreeFoundCheck)) * // number of visible trees up
                count(take_until($treesDown, $largerTreeFoundCheck)) // number of visible trees down
            )
        );
    }
}

echo 'Solution to Day 8-1: ' . $visibleFromOutsideTreesCount . PHP_EOL;
echo 'Solution to Day 8-2: ' . $maxScenicScore . PHP_EOL; die;
