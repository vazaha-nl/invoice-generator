<?php

namespace App\TimeEntryRenderers;

use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class Repository
{
    public function getAll(): array
    {
        $paths = [
            app_path('TimeEntryRenderers'),
        ];

        $all = [];

        foreach (Finder::create()->in($paths)->files() as $file) {
            $class = $this->filenameToClass($file);

            if (!is_subclass_of($class, TimeEntryRenderer::class)) {
                continue;
            }

            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            $all[] = new $class;
        }

        return $all;
    }

    public function getDefault(): TimeEntryRenderer
    {
        return new ByProject();
    }

    protected function filenameToClass(SplFileInfo $file): string
    {
        return '\\App\\' . str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($file->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
        );
    }
}
