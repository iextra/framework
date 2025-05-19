<?php

declare(strict_types=1);

namespace PlainWay;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

class Installer
{
    protected const DIR_PERMISSION = 0755;
    protected const FILE_PERMISSION = 0644;
    protected const GIT_KEEP_FILE = '.gitkeep';

    public static function postInstall(): void
    {
        self::message('Starting framework installation...');

        try {
            $projectRoot = self::initProjectRootDirectory();

            self::createAppStructure($projectRoot);
            self::copyFiles($projectRoot);

            self::message('Framework installed successfully!');
        } catch (Throwable $exception) {
            self::message("Error: {$exception->getMessage()}");
            exit(1);
        }
    }

    private static function copyFiles(string $projectRoot): void
    {
        $source = __DIR__ . '/resources/framework';
        self::assertDirectory($source);

        self::message("Copying framework files to: $projectRoot");

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $projectRoot . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    self::createDirectory($target);
                    self::createGitKeep($target);
                }

                continue;
            }

            if (!file_exists($target)) {
                self::copyFile($item->getPathname(), $target);
            }
        }
    }

    private static function createAppStructure(string $projectRoot): void
    {
        self::message('Creating application structure...');

        $structure = [
            'app/',
            'app/Middlewares/',
            'app/Controllers/',
            'bootstrap/',
            'config/',
            'public/',
            'public/assets/',
            'routes/',
            'storage/',
            'storage/cache/',
            'storage/logs/'
        ];

        foreach ($structure as $dir) {
            $fullPath = $projectRoot . DIRECTORY_SEPARATOR . $dir;

            if (!is_dir($fullPath)) {
                self::createDirectory($fullPath);
                self::createGitKeep($fullPath);
            }
        }
    }

    private static function createGitKeep(string $directory): void
    {
        $file = rtrim($directory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . self::GIT_KEEP_FILE;

        if (!file_exists($file)) {
            if (file_put_contents($file, '') === false) {
                throw new RuntimeException("Failed to create: " . $file);
            }

            chmod($file, self::FILE_PERMISSION);
        }
    }

    private static function message(string $message): void
    {
        echo $message . PHP_EOL;
    }

    private static function initProjectRootDirectory(): string
    {
        $projectRoot = self::determineProjectRoot();

        if (!is_dir($projectRoot)) {
            self::createDirectory($projectRoot);
        }

        self::assertDirectory($projectRoot);

        return $projectRoot;
    }

    private static function determineProjectRoot(): string
    {
        $possibleRoots = [
            dirname(__DIR__, 3), // vendor/package installation
            dirname(__DIR__, 4), // possible alternative
        ];

        foreach ($possibleRoots as $root) {
            if (file_exists($root . '/composer.json')) {
                return $root;
            }
        }

        return getcwd();
    }

    private static function createDirectory(string $directory): void
    {
        if (
            !mkdir($directory, self::DIR_PERMISSION, true)
            && !is_dir($directory)
        ) {
            throw new RuntimeException("Failed to create directory: $directory");
        }
    }

    private static function assertDirectory(string $path): void
    {
        if (!is_dir($path)) {
            throw new RuntimeException("Directory not found: $path");
        }

        if (!is_readable($path)) {
            throw new RuntimeException("Directory not readable: $path");
        }

        if (!is_writable($path)) {
            throw new RuntimeException("Directory not writable: $path");
        }
    }

    private static function copyFile(string $source, string $destination): void
    {
        if (!copy($source, $destination)) {
            throw new RuntimeException("Failed to copy: $source to $destination");
        }

        chmod($destination, self::FILE_PERMISSION);
    }
}
