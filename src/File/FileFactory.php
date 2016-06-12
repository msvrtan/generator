<?php

declare (strict_types = 1);
namespace NullDev\Skeleton\File;

use Composer\Autoload\ClassLoader;
use NullDev\Skeleton\Source\ImprovedClassSource;

class FileFactory
{
    /** @var ClassLoader */
    private $classLoader;
    /** @var array */
    private $paths;

    public function __construct(ClassLoader $classLoader, array $paths)
    {
        $this->classLoader = $classLoader;
        $this->paths       = $paths;
    }

    public function create(ImprovedClassSource $classSource) : FileResource
    {
        return new FileResource($this->classLoader, $this->getPathItBelongsTo($classSource), $classSource);
    }

    protected function getPathItBelongsTo(ImprovedClassSource $classSource)
    {
        foreach ($this->paths as $path) {
            if ($path->belongsTo($classSource->getFullName())) {
                return $path;
            }
        }

        throw new \Exception('Err 912123132: Cant find path that "'.$classSource->getFullName().'" would belong to!');
    }
}
