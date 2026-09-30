<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Count published project files on disk, independently of the Stache.
     */
    protected function publishedProjectFiles(string $contentPath): int
    {
        return collect(glob($contentPath.'/collections/projects/*/*.md'))
            ->reject(fn ($file) => preg_match('/^published: false$/m', file_get_contents($file)))
            ->count();
    }
}
