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

class FluentInterface
{
    /**
     * The path where the proxy classes are stored
     *
     * @var string
     */
    private $proxyPath;

    /**
     * The namespace prefix for the proxy classes
     *
     * @var string
     */
    private $namespacePrefix;

    /**
     * @param string $proxyPath
     * @param string $namespacePrefix
     */
    public function __construct($proxyPath, $namespacePrefix = 'Proxy')
    {
        $this->proxyPath = $proxyPath;
        $this->namespacePrefix = $namespacePrefix;

        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Generate a new proxy class file
     *
     * @param string $className The class name to be proxied
     * @param bool $reload Whether to force file reload
     * @return string The fully qualified proxy class name
     */
    public function create($className, $reload = false)
    {
        $proxy = new ProxyClassGenerator($className, $this->namespacePrefix);
        $class = $proxy->build();
        $fqcn = $proxy->getNamespace() . '\\' . $proxy->getClassName();
        $filename = $this->fqcn2Filename($fqcn);

        if (!file_exists($filename) || $reload) {
            file_put_contents($filename, $class);
        }

        return $fqcn;
    }

    /**
     * Creates a filename from a fully qualified class name
     *
     * @param string $fqcn
     * @return string
     */
    public function fqcn2Filename($fqcn)
    {
        $filename = str_replace('\\', '_', ltrim($fqcn, '\\')) . '.php';

        return $this->proxyPath . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Autoloader for the proxy classes
     *
     * @param $className
     */
    public function autoload($className)
    {
        if (file_exists($filename = $this->fqcn2Filename($className))) {
            require $filename;
        }
    }
}