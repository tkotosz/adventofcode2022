<?php

function pipe(callable ...$fns): callable
{
    return fn ($value) => array_reduce($fns, fn ($carry, callable $f) => $f($carry), $value);
}

function map(callable $f): callable
{
    return fn (array $value): array => array_map($f, $value);
}

function take(int $count): callable
{
    return fn (array $input): array => array_slice($input, 0, $count);
}

function split_by(string $separator): callable
{
    return fn (string $input): array => explode($separator, $input);
}

function sort_descending(array $input): array
{
    rsort($input);
    return $input;
}

$readAndNormalizeInput = pipe(
    'file_get_contents', // read file
    split_by("\n\n"), // split by empty line
    map(split_by("\n")), // split each by new line
    map('array_sum'), // sum each
);

$puzzle1Solver = pipe(
    $readAndNormalizeInput, // read input, convert to array of calorie sums per elf
    'max' // find the maximum
);

$puzzle2Solver = pipe(
    $readAndNormalizeInput, // read input, convert to array of calorie sums per elf
    'sort_descending', // sort results
    take(3), // take the top 3
    'array_sum' // calculate the sum
);

$inputFilePath = __DIR__ . '/input';
echo 'Solution to Day 1-1: ' . $puzzle1Solver($inputFilePath) . PHP_EOL;
echo 'Solution to Day 1-2: ' . $puzzle2Solver($inputFilePath) . PHP_EOL;

