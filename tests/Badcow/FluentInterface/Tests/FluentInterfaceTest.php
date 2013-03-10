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

use Badcow\FluentInterface\FluentInterface;

class FluentInterfaceTest extends TestCase
{
    public function testGenerate()
    {
        $fi = new FluentInterface($this->tmp_dir);
        $proxy_class = $fi->create('Badcow\FluentInterface\Tests\TestClass', true);
        $class = new $proxy_class();
        $this->assertInstanceOf('Badcow\FluentInterface\Tests\TestClass', $class->setLastName('Doe'));
    }
}