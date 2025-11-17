<?php

namespace Laravilt\Plugins\Commands\Concerns;

use Illuminate\Support\Facades\File;

trait HandlesFiles
{
    /**
     * Handle file copy operation with overwrite option.
     */
    public function handleFile(string $from, string $to, string $type = 'file'): void
    {
        $checkIfFileEx = $this->checkFile($to);

        if ($checkIfFileEx) {
            $this->deleteFile($to, $type);
            $this->copyFile($from, $to, $type);
        } else {
            $this->copyFile($from, $to, $type);
        }
    }

    /**
     * Check if file or directory exists.
     */
    public function checkFile(string $path): bool
    {
        return File::exists($path);
    }

    /**
     * Copy file or directory.
     */
    public function copyFile(string $from, string $to, string $type = 'file'): bool
    {
        // Ensure the destination directory exists
        $destinationDir = ($type === 'folder') ? $to : dirname($to);

        if (! File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        if ($type === 'folder') {
            $copy = File::copyDirectory($from, $to);
        } else {
            $copy = File::copy($from, $to);
        }

        return $copy;
    }

    /**
     * Delete file or directory.
     */
    public function deleteFile(string $path, string $type = 'file'): bool
    {
        if ($type === 'folder') {
            $delete = File::deleteDirectory($path);
        } else {
            $delete = File::delete($path);
        }

        return $delete;
    }

    /**
     * Ensure directory exists.
     */
    public function ensureDirectoryExists(string $path): void
    {
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * Move file or directory.
     */
    public function moveFile(string $from, string $to): bool
    {
        return File::move($from, $to);
    }

    /**
     * Create a directory with proper permissions.
     */
    public function createDirectory(string $path, int $mode = 0755, bool $recursive = true): bool
    {
        if (File::exists($path)) {
            return true;
        }

        return File::makeDirectory($path, $mode, $recursive);
    }
}
