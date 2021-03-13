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

use Kito\Type\Path;

/**
 * @author TheKito < blankitoracing@gmail.com >
 */
class Directory extends FileSystem
{
    public function __construct(Path $path)
    {
        parent::__construct($path);

        if (parent::exists() && !parent::isDirectory()) {
            throw new NotIsDirectoryException($path);
        }

        if (!$this->isRoot()) {
            parent::getParent();
        }
    }

    public function getChild($name)
    {
        $_ = parent::getChild($name);

        if (!self::pathExists($_)) {
            return new FileSystem($_, parent::getDirectorySeparator());
        } elseif (self::pathIsDirectory($_)) {
            return new Directory($_, parent::getDirectorySeparator());
        } elseif (self::pathIsFile($_)) {
            return new File($_, parent::getDirectorySeparator());
        } else {
            return new FileSystem($_, parent::getDirectorySeparator());
        }
    }

    public function getChildren()
    {
        $_ = [];
        foreach (self::getSubPaths($this) as $subPath) {
            $_ = $this->getChild($subPath->getName());
        }

        return $_;
    }

    final public function create()
    {
        if (parent::exists()) {
            return;
        }

        $this->getParent()->create();

        parent::validateWritable();

        if (!mkdir($this->__toString())) {
            throw new CreateDirectoryException($this->__toString());
        }
    }
}
