Badcow Fluid Interface
======================

Turns methods of any non-final class that do not return anything into chainable, fluid interfaces.

## Basic Usage

### Non fluid class

    <?php
    //sample_class.php

    namespace Sour\Milk;

    class FooBar
    {
        /**
         * @var string
         */
        private $firstName;

        /**
         * @var string
         */
        private $lastName;

        /**
         * @return string
         */
        public function getFirstName()
        {
            return $this->firstName;
        }

        /**
         * @return string
         */
        public function getLastName()
        {
            return $this->lastName;
        }

        /**
         * @param string $firstName
         */
        public function setFirstName($firstName)
        {
            $this->firstName = $firstName;
        }

        /**
         * @param string $lastName
         */
        public function setLastName($lastName)
        {
            $this->lastName = $lastName;
        }
    }

### Create fluid proxy

    <?php

    use Badcow\FluidInterface\FluidInterface;

    $fi = new FluidInterface(__DIR__ . '/Proxies');
    $proxy = $fi->create('Sour\Milk\Foobar');
    $foobar = new $proxy();

    //Class is now a fluid interface
    $foobar->setFirstName('Sam')->setLastName('Williams');

    echo $foobar->getFirstName() . ' ' . $foobar->getLastName();