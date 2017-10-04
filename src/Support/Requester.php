<?php

namespace TwoDojo\ModuleManager\Support;

class Requester
{
    protected $basePath;

    /**
     * Create a new instance
     *
     * @return self
     */
    public static function make()
    {
        return new static();
    }

    /**
     * Set the base path
     *
     * @param $path
     * @return $this
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
        if (!ends_with($this->basePath, DIRECTORY_SEPARATOR)) {
            $this->basePath .= DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * Determine if the given file is exists
     *
     * @param string $file
     * @return boolean
     */
    public function exists(string $file) : bool
    {
        return file_exists($this->path($file));
    }

    /**
     * Get the path for the given file
     *
     * @param string $file
     * @return string
     */
    public function path(string $file)
    {
        return $this->basePath.$file;
    }

    /**
     * Get all files in the specified directory
     *
     * @param $path
     * @return array
     */
    public function getFiles($path)
    {
        $files = [];

        foreach (new \DirectoryIterator($this->path($path)) as $file) {
            if ($file->isFile()) {
                $files[] = $file->getFileInfo();
            }
        }

        return $files;
    }
}
