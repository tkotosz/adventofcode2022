<?php

class InMemoryDirectory
{
    public function __construct(
        public ?InMemoryDirectory $parent,
        public string $name,
        public array $children
    ) {
    }

    public function hasDirectoryNamed(string $directoryName): bool
    {
        foreach ($this->children as $item) {
            if ($item instanceof InMemoryDirectory && $item->name === $directoryName) {
                return true;
            }
        }

        return false;
    }

    public function hasFileNamed(string $fileName): bool
    {
        foreach ($this->children as $item) {
            if ($item instanceof InMemoryFile && $item->name === $fileName) {
                return true;
            }
        }

        return false;
    }

    public function createDirectoryNamed(string $directoryName): void
    {
        $this->children[] = new InMemoryDirectory($this, $directoryName, []);
    }

    public function createFileNamed(string $fileName, int $size): void
    {
        $this->children[] = new InMemoryFile($fileName, $size);
    }

    public function getDirectoryNamed(string $directoryName): InMemoryDirectory
    {
        foreach ($this->children as $item) {
            if ($item instanceof InMemoryDirectory && $item->name === $directoryName) {
                return $item;
            }
        }

        throw new Exception("Error");
    }

    public function directories(): array
    {
        $directories = [];

        foreach ($this->children as $child) {
            if ($child instanceof InMemoryDirectory) {
                $directories[] = $child;
                $directories = array_merge($directories, $child->directories());
            }
        }

        return $directories;
    }

    public function totalSize(): int
    {
        $totalSize = 0;

        foreach ($this->children as $child) {
            $totalSize += $child->totalSize();
        }

        return $totalSize;
    }
}

class InMemoryFile
{
    public function __construct(
        public string $name,
        public int $size
    ) {
    }

    public function totalSize(): int
    {
        return $this->size;
    }
}

class InMemoryFileSystem
{
    public InMemoryDirectory $root;
    public InMemoryDirectory $location;

    public function __construct()
    {
        $this->root = new InMemoryDirectory(null, '/', []);
        $this->location = $this->root;
    }

    public function isDirectoryExists(string $directoryName)
    {
        return $this->location->hasDirectoryNamed($directoryName);
    }

    public function isFileExists(string $fileName)
    {
        return $this->location->hasFileNamed($fileName);
    }

    public function createDirectory(string $directoryName)
    {
        $this->location->createDirectoryNamed($directoryName);
    }

    public function createFile(string $fileName, int $size)
    {
        $this->location->createFileNamed($fileName, $size);
    }

    public function moveToDirectory(string $directoryName)
    {
        if ($directoryName === '/') {
            $this->location = $this->root;
        } elseif ($directoryName === "..") {
            $this->location = $this->location->parent ?? $this->location;
        } else {
            $this->location = $this->location->getDirectoryNamed($directoryName);
        }
    }

    public function print($printLocation = null, $indentation = "  ")
    {
        if ($printLocation === null) {
            $printLocation = $this->root;
            echo "- / (dir)" . PHP_EOL;
        }

        foreach ($printLocation->children as $child) {
            if ($child instanceof InMemoryFile) {
                echo sprintf('%s- %s (file, size=%s)', $indentation, $child->name, $child->size) . PHP_EOL;
            } else {
                echo sprintf('%s- %s (dir)', $indentation, $child->name) . PHP_EOL;
                $this->print($child, $indentation . "  ");
            }
        }
    }

    public function directorySizes(): array
    {
        $totalSizes = [$this->root->totalSize()];

        foreach ($this->root->directories() as $directory) {
            $totalSizes[] = $directory->totalSize();
        }

        return $totalSizes;
    }

    public function freeSpace(): int
    {
        return 70000000 - $this->root->totalSize();
    }
}


$inputFilePath = __DIR__ . '/input';

$lines = explode("\n", file_get_contents($inputFilePath));
$fileSystem = new InMemoryFileSystem();

foreach ($lines as $line) {
    if (strpos($line, '$ cd') === 0) {
        [,,$directory] = explode(" ", $line);
        if (!in_array($directory, ['/', '..']) && !$fileSystem->isDirectoryExists($directory)) {
            $fileSystem->createDirectory($directory);
        }
        $fileSystem->moveToDirectory($directory);
        continue;
    }

    if (strpos($line, '$ ls') === 0) {
        continue;
    }

    [$size, $name] = explode(" ", $line);

    if ($size === "dir") {
        if (!$fileSystem->isDirectoryExists($name)) {
            $fileSystem->createDirectory($name);
        }
    } else {
        if (!$fileSystem->isFileExists($name)) {
            $fileSystem->createFile($name, $size);
        }
    }
}

//$fileSystem->print();

// Part 1
$result = array_sum(array_filter($fileSystem->directorySizes(), fn ($size) => $size < 100000));
echo 'Solution to Day 7-1: ' . $result . PHP_EOL;

// Part 2
$neededSpace = 30000000 - $fileSystem->freeSpace();
$result = min(array_filter($fileSystem->directorySizes(), fn ($size) => $size >= $neededSpace));
echo 'Solution to Day 7-2: ' . $result . PHP_EOL;
