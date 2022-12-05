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

function string_replace(string $search): callable
{
    return fn (string $replace) => fn (string $subject) => str_replace($search, $replace, $subject);
}

function drop_last(array $input): array
{
    array_pop($input);

    return $input;
}

function head(array $input): mixed
{
    return array_shift($input);
}

function restructure(array $rows): array
{
    $crates = [];

    foreach ($rows as $row) {
        foreach ($row as $index => $crate) {
            if ($crate === " " || $crate === "[" || $crate === "]") continue;
            $crates[(int)($index/4) + 1][] = $crate;
        }
    }

    ksort($crates);

    return $crates;
}

function apply_operations(callable $itemCollector): callable
{
    return fn (array $operations) => function (array $crates) use ($itemCollector, $operations) {
        foreach ($operations as $operation) {
            [$qty, $from, $to] = $operation;

            $items = [];
            for ($i=1; $i<=$qty; $i++) {
                $itemCollector($items, array_shift($crates[$from]));
            }

            array_unshift($crates[$to], ...$items);
        }

        return $crates;
    };
}

$inputFilePath = __DIR__ . '/input';

[$crates, $operations] = pipe(
    'file_get_contents',
    split_by("\n\n"),
)($inputFilePath);

$crates = pipe(
    split_by("\n"),
    'drop_last',
    map('str_split'),
    'restructure'
)($crates);

$operations = pipe(
    string_replace("move ")(""),
    string_replace(" from ")("_"),
    string_replace(" to ")("_"),
    split_by("\n"),
    map(split_by("_"))
)($operations);

$puzzleSolver = fn ($itemCollector) => pipe(
    apply_operations($itemCollector)($operations),
    map('head'),
    'join'
);

$puzzle1Solver = $puzzleSolver('array_unshift');
$puzzle2Solver = $puzzleSolver('array_push');

echo 'Solution to Day 5-1: ' . $puzzle1Solver($crates) . PHP_EOL;
echo 'Solution to Day 5-2: ' . $puzzle2Solver($crates) . PHP_EOL;

