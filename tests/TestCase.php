<?php

namespace Tests;

use Emuravjev\Mdash\Typograph;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base class for all tests.
 */
class TestCase extends PHPUnitTestCase
{
    /**
     * Array of tests ['text' => 'Text to test', 'result' => 'Expected result']
     *
     * @var array
     */
    protected  $tests;

    /**
     * @var Typograph
     */
    protected $typographer;

    /**
     * Test setup, runs before each test case and sets up typographer
     *
     * @return void
     */
    final public function setUp(): void
    {
        parent::setUp();
        $this->typographer = new Typograph();
        // Adding typographer options for tests
        $this->typographer->setup([
            'OptAlign.all' => 'off',
            'Text.paragraphs' => 'off',
            'Text.breakline' => 'off',
            'Number.numeric_sub' => 'off'
        ]);
    }

    /**
     * Runs tests
     *
     * @return void
     */
    final public function runTypographerTests(): void
    {
        foreach ($this->tests as $test) {
            $this->typographer->set_text($test['text']);
            $this->assertEquals($test['result'], $this->typographer->apply());
        }
    }
}