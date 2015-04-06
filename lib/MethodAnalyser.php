<?php
/*
 * This file is part of the Fluent Interface Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\FluentInterface;

class MethodAnalyser
{
    /**
     * @var \ReflectionMethod
     */
    private $method;

    /**
     * @var string
     */
    private $methodLogic;

    /**
     * @param \ReflectionMethod $method
     */
    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
        $this->methodLogic = self::stripPhpComments($this->_getMethodLogic($this->method));
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    private function _getMethodLogic(\ReflectionMethod $method)
    {
        $classFile = file_get_contents($method->getFileName());
        $lines = explode("\n", $classFile);
        $lines = array_slice($lines, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        return implode("\n", $lines);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function stripPhpComments($string)
    {
        $string = preg_replace('/#.*$/m', '', $string);
        $string = preg_replace('/\/\/.*$/m', '', $string);
        $string = preg_replace('/\/\*.*(?=\*\/)\*\//s', '', $string);

        return $string;
    }

    /**
     * Determine if there is a return control
     *
     * @return bool
     */
    public function hasReturnLogic()
    {
        return (preg_match('/[\)\{:;\?](?:\s)?+return(\s|;)/i', $this->methodLogic) === 1);
    }
}