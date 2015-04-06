<?php
/*
 * This file is part of the Fluent Interface Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\FluentInterface\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string The directory to store generated classes
     */
    protected $tmpDir;

    /**
     * @var array
     */
    protected $tmpFiles = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->tmpDir = sys_get_temp_dir();
    }

    /**
     * Destroy the generated temporary files
     */
    public function __destruct()
    {
        foreach ($this->tmpFiles as $file) {
            unlink($file);
        }
    }
}