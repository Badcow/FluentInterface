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

class ProxyClassGenerator
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var \ReflectionClass
     */
    private $classReflection;

    /**
     * @var string
     */
    private $proxyNamespace;

    private $skippedMethods = array(
        '__sleep'   => true,
        '__clone'   => true,
        '__wakeup'  => true,
        '__get'     => true,
        '__set'     => true,
        '__isset'   => true,
    );

    /**
     * @var string
     */
    protected $classTemplate = <<<STRING
<?php

namespace <namespace>;

/**
 * Auto generated proxy class
 */
class <className> extends \<baseClass>
{
<methods>
}
STRING;

    /**
     * @param $className
     * @param string $proxyNamespace
     */
    public function __construct($className, $proxyNamespace = 'Proxy')
    {
        $this->className = $className;
        $this->proxyNamespace = $proxyNamespace;
        $this->classReflection = new \ReflectionClass($className);
    }

    /**
     * Builds the proxy class
     *
     * @return string Full proxy class file
     */
    public function build()
    {
        $patterns = array(
            '/<namespace>/',
            '/<className>/',
            '/<baseClass>/',
            '/<methods>/',
        );

        $replacements = array(
            $this->getNamespace(),
            $this->getClassName(),
            $this->getBaseClass(),
            $this->generateMethods($this->classReflection),
        );

        return preg_replace($patterns, $replacements, $this->classTemplate);
    }

    /**
     * Generates decorated methods by picking those available in the parent class
     *
     * @param \ReflectionClass $class
     * @return string
     * @throws \ErrorException
     */
    protected function generateMethods(\ReflectionClass $class)
    {
        $methods           = '';
        $methodNames       = array();
        $reflectionMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $method) {
            $name = $method->getName();

            if (
                $method->isConstructor() ||
                isset($this->skippedMethods[strtolower($name)]) ||
                isset($methodNames[$name]) ||
                $method->isFinal() ||
                $method->isStatic() ||
                ( ! $method->isPublic())
            ) {
                continue;
            }

            $methodNames[$name] = true;
            $methods .= "\n    /**\n"
                . "     * {@inheritDoc}\n"
                . "     */\n"
                . '    public function ';

            if ($method->returnsReference()) {
                $methods .= '&';
            }

            $methods .= $name . '(';

            $firstParam      = true;
            $parameterString = '';
            $argumentString  = '';
            $parameters      = array();

            foreach ($method->getParameters() as $param) {
                if ($firstParam) {
                    $firstParam = false;
                } else {
                    $parameterString .= ', ';
                    $argumentString  .= ', ';
                }

                try {
                    $paramClass = $param->getClass();
                } catch (\ReflectionException $previous) {
                    throw new \ErrorException;
                }

                // We need to pick the type hint class too
                if (null !== $paramClass) {
                    $parameterString .= '\\' . $paramClass->getName() . ' ';
                } elseif ($param->isArray()) {
                    $parameterString .= 'array ';
                }

                if ($param->isPassedByReference()) {
                    $parameterString .= '&';
                }

                $parameters[] = '$' . $param->getName();
                $parameterString .= '$' . $param->getName();
                $argumentString  .= '$' . $param->getName();

                if ($param->isDefaultValueAvailable()) {
                    $parameterString .= ' = ' . var_export($param->getDefaultValue(), true);
                }
            }

            $methods .= $parameterString . ')';
            $methods .= "\n" . '    {' . "\n";

            $_method = new MethodAnalyser($method);

            if ($_method->hasReturnLogic()) {
                $methods .= "        return parent::" . $name . '(' . $argumentString . ');';
            } else {
                $methods .= "        parent::" . $name . '(' . $argumentString . ');';
                $methods .= "\n\n        return \$this;";
            }

            $methods .= "\n" . '    }' . "\n";
        }

        return $methods;
    }

    /**
     * Get the fully qualified proxy class name
     *
     * @return string
     */
    public function getNamespace()
    {
        $namespace = $this->proxyNamespace;

        if ('' !== $nsn = $this->classReflection->getNamespaceName()) {
            $namespace .= '\\' . $nsn;
        }

        return $namespace;
    }

    /**
     * Get the name of the class
     *
     * @return string
     */
    public function getClassName()
    {
        $array = explode('\\', $this->classReflection->getName());
        return end($array);
    }

    /**
     * Get the fully qualified class name of origin class
     *
     * @return string
     */
    public function getBaseClass()
    {
        return $this->classReflection->getName();
    }
}