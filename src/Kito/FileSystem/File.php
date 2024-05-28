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

namespace Kito\FileSystem;

use Kito\Path\Path;

/**
 * @author TheKito < blankitoracing@gmail.com >
 */
class File extends FileSystem
{
    public static function pathFileSize(Path $path)
    {
        self::pathValidateReadable($path);

        return filesize($path->__toString());
    }

    public function __construct(Path $path)
    {
        parent::__construct($path);

        if (parent::exists() && !parent::isFile()) {
            throw new NotIsFileException($path);
        }
    }

    final public function getSize()
    {
        return self::pathFileSize($this);
    }

    final public function delete()
    {
        if (!parent::exists()) {
            return;
        }

        parent::validateWritable();
        if (!unlink($this->__toString())) {
            throw new IOException($this);
        }
    }

    final public function copyTo(File $destination)
    {
        if (copy($this->__toString(), $destination->__toString()) === false) {
            throw new CopyFileException($this->__toString().' > '.$destination->__toString());
        }
    }

    public function getContent()
    {
        parent::validateReadable();

        return file_get_contents($this->__toString());
    }

    public function setContent($content)
    {
        parent::validateWritable();

        return file_put_contents($this->__toString(), $content);
    }

    public function touch()
    {
        parent::validateExistence();
        parent::validateWritable();
        touch($this->__toString());
    }

    final public function create()
    {
        if (parent::exists()) {
            return;
        }

        $this->getParent()->create();

        parent::validateWritable();

        if (!touch($this->__toString())) {
            throw new CreateFileException($this->__toString());
        }
    }
}
