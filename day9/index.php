<?php

class SnakePart
{
    public function __construct(
        public int $positionX,
        public int $positionY,
        public array $positionHistory = []
    ) {
        $this->positionHistory = [sprintf('%d-%d', $positionX, $positionY)];
    }
    
    public function moveBy(int $x, int $y)
    {
        $this->positionX += $x;
        $this->positionY += $y;
        $this->positionHistory[] = sprintf('%d-%d', $this->positionX, $this->positionY);
    }
}

class Snake
{
    public SnakePart $head;
    public array $tailParts;

    public function __construct(int $tailLenght)
    {
        $this->head = new SnakePart(0,0);

        for ($i = 0; $i < $tailLenght; $i++) {
            $this->tailParts[$i] = new SnakePart(0, 0);
        }
    }

    public function move(string $direction): void
    {
        $this->head->moveBy(
            match ($direction) {
                'R' => 1,
                'L' => -1,
                default => 0
            },
            match ($direction) {
                'U' => 1,
                'D' => -1,
                default => 0
            }
        );

        $previousPart = $this->head;
        foreach ($this->tailParts as $tailPart) {
            $previousPartXDistance = $previousPart->positionX - $tailPart->positionX;
            $previousPartYDistance = $previousPart->positionY - $tailPart->positionY;

            if (abs($previousPartXDistance) > 1 || abs($previousPartYDistance) > 1) {
                $tailPart->moveBy(
                    ($previousPartXDistance !== 0) ? ($previousPartXDistance / abs($previousPartXDistance)) : 0,
                    ($previousPartYDistance !== 0) ? ($previousPartYDistance / abs($previousPartYDistance)) : 0
                );
            }

            $previousPart = $tailPart;
        }
    }
}

function parse_input(string $input): array
{
    $movements = [];

    foreach (explode(PHP_EOL, $input) as $line) {
        [$direction, $amount] = explode(" ", $line);
        for ($i = 0; $i < $amount; $i++) {
            $movements[] = $direction;
        }
    }

    return $movements;
}

function solve(int $tailLenght, array $movements): int
{
    $snake = new Snake($tailLenght);

    foreach ($movements as $movement) {
        $snake->move($movement);
    }

    return count(array_unique(end($snake->tailParts)->positionHistory));
}

$movements = parse_input(file_get_contents(__DIR__ . '/input'));

echo 'Solution to Day 9-1: ' . solve(1, $movements) . PHP_EOL;
echo 'Solution to Day 9-2: ' . solve(9, $movements) . PHP_EOL;