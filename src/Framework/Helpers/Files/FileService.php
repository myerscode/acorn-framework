<?php

namespace Myerscode\Acorn\Framework\Helpers\Files;

use Myerscode\Utilities\Bags\Utility as Bag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FileService
{

    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function ensureDirectory($dir)
    {
        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir);
        }
    }

    public function exists($file): bool
    {
        return $this->filesystem->exists($file);
    }

    public function getFullyQualifiedClassname($filename)
    {
        return $this->getNamespace($filename).'\\'.$this->getClassname($filename);
    }

    public function getClassname($filename)
    {
        $directoriesAndFilename = explode('/', $filename);
        $filename = array_pop($directoriesAndFilename);
        $nameAndExtension = explode('.', $filename);

        return array_shift($nameAndExtension);
    }

    public function getNamespace($filename)
    {
        $lines = file($filename);
        $array = preg_grep('/^namespace /', $lines);
        $namespaceLine = array_shift($array);
        $match = array();
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);

        return array_pop($match);
    }

    public function filesIn(string $directory)
    {
        $finder = new Finder();
        $bag = new Bag([]);
        try {
            return $bag->push(...iterator_to_array($finder->files()->in($directory)->getIterator(), false));
        } catch (\Exception $exception) {
            return $bag;
        }
    }

}
