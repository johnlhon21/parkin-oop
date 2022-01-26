<?php

use Faker\Generator;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp(): void
    {
        $this->faker = app()->make(\Faker\Generator::class);
        parent::setUp();
    }
}
