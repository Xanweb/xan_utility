<?php
namespace XanUtility\Object;

/**
 * Class usefull to get or set private/protected properties of an object.
 * But in anycase it's a Hack and should be avoided.
 */
class Hacker
{
    /**
     * @var \Closure
     */
    private $getterFunction;

    /**
     * @var \Closure
     */
    private $setterFunction;

    public function __construct($object)
    {
        if (!is_object($object)) {
            throw new \Exception(t('Object to be Hacked cannot be null'));
        }

        $getter = function ($property) {
            return $this->{$property};
        };
        $setter = function ($property, $value) {
            return $this->{$property} = $value;
        };

        $this->getterFunction = $getter->bindTo($object, get_class($object));
        $this->setterFunction = $setter->bindTo($object, get_class($object));
    }

    public function get($property)
    {
        return $this->getterFunction($property);
    }

    public function set($property, $value)
    {
        $this->setterFunction($property, $value);
    }
}
