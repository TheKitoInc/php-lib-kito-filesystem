<?php

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 */

namespace Kito\Storage\FileSystem;

use Kito\Path\Path;

/**
 * @author TheKito < blankitoracing@gmail.com >
 */
class FileSystem extends Path
{
    public static function pathValidateIsDirectory(Path $path)
    {
        if (!self::pathIsDirectory($path)) {
            throw new NotIsDirectoryException($path);
        }
    }

    public static function pathValidateIsFile(Path $path)
    {
        if (!self::pathIsFile($path)) {
            throw new NotIsFileException($path);
        }
    }

    public static function pathValidateIsLink(Path $path)
    {
        if (!self::pathIsLink($path)) {
            throw new NotIsLinkException($path);
        }
    }

    public static function pathValidateReadable(Path $path)
    {
        if (!self::pathIsReadable($path)) {
            throw new NotIsReadableException($path);
        }
    }

    public static function pathValidateWritable(Path $path)
    {
        if (!self::pathIsWritable($path)) {
            throw new NotIsWritableException($path);
        }
    }

    public static function pathValidateExistence(Path $path)
    {
        if (!self::pathExists($path)) {
            throw new NotFoundException($path);
        }
    }

    public static function pathExists(Path $path)
    {
        return file_exists($path->__toString());
    }

    public static function pathIsFile(Path $path)
    {
        self::pathValidateExistence($path);

        return is_file($path->__toString());
    }

    public static function pathIsLink(Path $path)
    {
        self::pathValidateExistence($path);

        return is_link($path->__toString());
    }

    public static function pathIsDirectory(Path $path)
    {
        self::pathValidateExistence($path);

        return is_dir($path->__toString());
    }

    public static function pathIsReadable(Path $path)
    {
        self::pathValidateExistence($path);

        return is_readable($path->__toString());
    }

    public static function pathGetModificationTime(Path $path)
    {
        self::pathValidateExistence($path);

        return filemtime($path->__toString());
    }

    public static function pathGetFreeSpace(Path $path)
    {
        self::pathValidateExistence($path);

        return disk_free_space($path->__toString());
    }

    public static function pathGetTotalSpace(Path $path)
    {
        self::pathValidateExistence($path);

        return disk_total_space($path->__toString());
    }

    public static function pathSetModificationTime(Path $path, $time)
    {
        self::pathValidateExistence($path);
        self::pathValidateWritable($path);
        if (!touch($path->__toString(), $time)) {
            throw new IOException($path);
        }
    }

    public static function pathFreeSpace(Path $path)
    {
        self::pathValidateExistence($path);

        return disk_free_space($path->__toString());
    }

    public static function pathIsWritable(Path $path)
    {
        if (self::pathExists($path)) {
            return is_writable($path->__toString());
        } else {
            self::pathValidateExistence($path->getParent());

            return is_writable($path->getParent()->__toString());
        }
    }

    public static function getSubPaths(Path $path)
    {
        self::pathValidateIsDirectory($path);

        $tmp = [];
        foreach (scandir($path->__toString()) as $_) {
            if ($_ == '.') {
                continue;
            }

            if ($_ == '..') {
                continue;
            }

            $tmp[] = $path->getChild($_);
        }

        return $tmp;
    }

    public function exists()
    {
        return self::pathExists($this);
    }

    public function isFile()
    {
        return self::pathIsFile($this);
    }

    public function isLink()
    {
        return self::pathIsLink($this);
    }

    public function getParent()
    {
        return new Directory(parent::getParent(), parent::getDirectorySeparator());
    }

    public function isReadable()
    {
        return self::pathIsReadable($this);
    }

    public function isWritable()
    {
        return self::pathIsWritable($this);
    }

    public function isDirectory()
    {
        return self::pathIsDirectory($this);
    }

    public function getFreeSpace()
    {
        return self::pathGetFreeSpace($this);
    }

    public function getTotalSpace()
    {
        return self::pathGetTotalSpace($this);
    }

    public function getModificationTime()
    {
        return self::pathGetModificationTime($this);
    }

    public function setModificationTime($time)
    {
        return self::pathSetModificationTime($this, $time);
    }

    protected function validateExistence()
    {
        self::pathValidateExistence($this);
    }

    protected function validateReadable()
    {
        self::pathValidateReadable($this);
    }

    protected function validateWritable()
    {
        self::pathValidateWritable($this);
    }

    protected function validateIsDirectory()
    {
        return self::pathValidateIsDirectory($this);
    }

    protected function validateIsFile()
    {
        return self::pathValidateIsFile($this);
    }

    protected function validateIsLink()
    {
        return self::pathValidateIsLink($this);
    }

    protected function __construct(Path $path)
    {
        parent::__construct($path->pathElements, $path->getDirectorySeparator());
    }

    public function combinePath(Path $subPath)
    {
        return new FileSystem(parent::combinePath($subPath), parent::getDirectorySeparator());
    }
}
