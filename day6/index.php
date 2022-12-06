<?php

function solve_puzzle(string $input, int $markerLength): int
{
    $offset = 0;
    do {
        $chunk = str_split(substr($input, $offset++, $markerLength));
    } while ($chunk !== array_unique($chunk));

    return $offset - 1 + $markerLength;
}

$inputFilePath = __DIR__ . '/input';
$input = file_get_contents($inputFilePath);

echo 'Solution to Day 6-1: ' . solve_puzzle($input, 4) . PHP_EOL;
echo 'Solution to Day 6-2: ' . solve_puzzle($input, 14) . PHP_EOL;
