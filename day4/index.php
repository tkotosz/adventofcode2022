<?php

final class Range
{
    public static function create(int $from, int $to)
    {
        return new self($from, $to);
    }

    public function contains(Range $other): bool
    {
        return ($this->from <= $other->from && $this->to >= $other->to);
    }

    public function overlap(Range $other): bool
    {
        return ($other->from <= $this->from && $other->to >= $this->from) || ($other->from <= $this->to && $other->to >= $this->to);
    }

    private function __construct(
        public readonly int $from,
        public readonly int $to
    ) {
    }
}

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

function convert_to_ranges(array $input): array
{
    return [Range::create(...$input[0]), Range::create(...$input[1])];
}

function check_range_fully_contain_other(array $ranges): bool
{
    return $ranges[0]->contains($ranges[1]) || $ranges[1]->contains($ranges[0]);
}

function check_range_overlap_other(array $ranges): bool
{
    return $ranges[0]->overlap($ranges[1]) || $ranges[1]->overlap($ranges[0]);
}

$puzzleSolver = fn ($strategy) => pipe(
    'file_get_contents', // read file
    split_by("\n"), // separate by lines,
    map(split_by(",")), // form groups
    map(map(split_by("-"))), // form ranges
    map('convert_to_ranges'), // convert to Range objects
    map($strategy),
    'array_filter', // remove false values
    'count' // count the number of true values
);

$puzzle1Solver = $puzzleSolver('check_range_fully_contain_other');
$puzzle2Solver = $puzzleSolver('check_range_overlap_other');

$inputFilePath = __DIR__ . '/input';
echo 'Solution to Day 4-1: ' . $puzzle1Solver($inputFilePath) . PHP_EOL;
echo 'Solution to Day 4-2: ' . $puzzle2Solver($inputFilePath) . PHP_EOL;

