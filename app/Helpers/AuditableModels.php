<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use ReflectionClass;

class AuditableModels
{
    /**
     * Returns a list of auditable models available in the system.
     * Keys are the fully-qualified class names (used as auditable_type in DB),
     * values are human-readable labels.
     *
     * @return array<string, string>
     */
    public static function getList(): array
    {
        $models = [];

        foreach (glob(app_path('Models/*.php')) as $file) {
            $class = 'App\\Models\\' . basename($file, '.php');

            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract() || ! $reflection->implementsInterface(Auditable::class)) {
                continue;
            }

            $models[$class] = Str::headline(basename($file, '.php'));
        }

        asort($models);

        return $models;
    }
}
