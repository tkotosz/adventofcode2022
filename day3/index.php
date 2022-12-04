<?php

function pipe(callable ...$fns): callable
{
    return fn ($value) => array_reduce($fns, fn ($carry, callable $f) => $f($carry), $value);
}

function map(callable $f): callable
{
    return fn (array $value): array => array_map($f, $value);
}

function split_by(string $separator): callable
{
    return fn (string $input): array => explode($separator, $input);
}

function cut_in_half(string $input): array
{
    $halfIndex = strlen($input) / 2;
    return [substr($input, 0, $halfIndex), substr($input, $halfIndex)];
}

function intersect_items(array $input): array
{
    return array_intersect(...$input);
}

function head(array $input): mixed
{
    return array_shift($input);
}

function convert_to_priority_number(string $char): int
{
    // a-z = 1-26, A-Z = 27-52
    return (strtolower($char) === $char) ? ord($char) - 96 : ord($char) - 38;
}

function generate_groups_of(int $size): callable
{
    return fn (array $input) => array_chunk($input, $size);
}

$puzzleSolver = fn ($groupGeneratorStrategy) => pipe(
    'file_get_contents', // read file
    split_by("\n"), // separate by lines
    $groupGeneratorStrategy,
    map(map('str_split')), // convert strings to character list
    map(map('array_unique')), // eliminate duplicated item types
    map('intersect_items'), // find the item present in all list within the group
    map('head'), // grab that 1 item (as per description there should only be 1)
    map('convert_to_priority_number'), // covert characters to priority number
    'array_sum' // sum them up
);

$puzzle1Solver = $puzzleSolver(map('cut_in_half')); // separate each into 2 halves
$puzzle2Solver = $puzzleSolver(generate_groups_of(3)); //  generate group of 3 elves

$inputFilePath = __DIR__ . '/input';
echo 'Solution to Day 3-1: ' . $puzzle1Solver($inputFilePath) . PHP_EOL;
echo 'Solution to Day 3-2: ' . $puzzle2Solver($inputFilePath) . PHP_EOL;

