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
    protected $tmp_dir;

    public function __construct()
    {
        $this->tmp_dir = __DIR__ . '/../../../tmp';
    }
}