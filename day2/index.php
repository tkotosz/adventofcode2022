<?php

enum RockPaperScissorsShape
{
    case Rock;
    case Paper;
    case Scissors;

    public static function fromChar(string $char): self
    {
        return match ($char) {
            'A' => self::Rock,
            'B' => self::Paper,
            'C' => self::Scissors,
        };
    }

    public static function loosePairOf(RockPaperScissorsShape $other): RockPaperScissorsShape
    {
        return match($other) {
            self::Rock => self::Scissors,
            self::Paper => self::Rock,
            self::Scissors => self::Paper
        };
    }

    public static function drawPairOf(RockPaperScissorsShape $other): RockPaperScissorsShape
    {
        return match($other) {
            self::Rock => self::Rock,
            self::Paper => self::Paper,
            self::Scissors => self::Scissors
        };
    }

    public static function winPairOf(RockPaperScissorsShape $other): RockPaperScissorsShape
    {
        return match($other) {
            self::Rock => self::Paper,
            self::Paper => self::Scissors,
            self::Scissors => self::Rock
        };
    }

    public function matchScoreAgainst(RockPaperScissorsShape $enemy): int
    {
        return $this->shapeScore() + $this->roundOutcomeScoreAgainst($enemy);
    }

    private function shapeScore(): int
    {
        return match($this) {
            self::Rock => 1,
            self::Paper => 2,
            self::Scissors => 3
        };
    }

    private function roundOutcomeScoreAgainst(RockPaperScissorsShape $enemy): int
    {
        return match($this) {
            self::Rock => match($enemy) {
                self::Rock => 3,
                self::Paper => 0,
                self::Scissors => 6
            },
            self::Paper => match($enemy) {
                self::Rock => 6,
                self::Paper => 3,
                self::Scissors => 0
            },
            self::Scissors => match($enemy) {
                self::Rock => 0,
                self::Paper => 6,
                self::Scissors => 3
            },
        };
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

function puzzle1_strategy(array $input): array
{
    $enemy = RockPaperScissorsShape::fromChar($input[0]);

    $payer = match ($input[1]) {
        'X' => RockPaperScissorsShape::Rock,
        'Y' => RockPaperScissorsShape::Paper,
        'Z' => RockPaperScissorsShape::Scissors,
    };

    return [$enemy, $payer];
}

function puzzle2_strategy(array $input): array
{
    $enemy = RockPaperScissorsShape::fromChar($input[0]);

    $payer = match ($input[1]) {
        'X' => RockPaperScissorsShape::loosePairOf($enemy),
        'Y' => RockPaperScissorsShape::drawPairOf($enemy),
        'Z' => RockPaperScissorsShape::winPairOf($enemy),
    };

    return [$enemy, $payer];
}

function calculate_score(array $input): int
{
    [$enemy, $player] = $input;

    return $player->matchScoreAgainst($enemy);
}

$puzzleSolver = fn ($strategy) => pipe(
    'file_get_contents', // read file
    split_by("\n"), // split by line
    map(split_by(" ")), // split each by space
    map($strategy), // calculate the choices for the enemy and the player
    map('calculate_score'), // calculate score for each match
    'array_sum' // calculate total score
);

$puzzle1Solver = $puzzleSolver('puzzle1_strategy');
$puzzle2Solver = $puzzleSolver('puzzle2_strategy');

$inputFilePath = __DIR__ . '/input';
echo 'Solution to Day 2-1: ' . $puzzle1Solver($inputFilePath) . PHP_EOL;
echo 'Solution to Day 2-2: ' . $puzzle2Solver($inputFilePath) . PHP_EOL;

