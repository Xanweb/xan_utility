<?php
namespace XanUtility\Foundation;

use Concrete\Core\Foundation\ConcreteObject as CoreObject;
use Doctrine\Common\Collections\ArrayCollection;

abstract class ConcreteObject extends CoreObject implements \JsonSerializable
{
    /**
     * used for json serialisation.
     *
     * @var \stdClass
     */
    private $jsonObj;

    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $setter = 'set' . ucfirst($key);
            // we prefer passing by setter method
            if (method_exists($this, $setter)) {
                call_user_func([$this, $setter], $prop);
            } else {
                $this->{$key} = $prop;
            }
        }
    }

    public function jsonSerialize()
    {
        $dh = c5app('date');
        $this->jsonObj = new \stdClass();
        $array = get_object_vars($this);
        foreach ($array as $key => $v) {
            if ($v && ($v instanceof \DateTime)) {
                $this->jsonObj->{$key} = $dh->formatCustom('d.m.Y', $v);
            } elseif (is_object($v)) {
                $this->jsonSerializeRelatedObj($key, $v);
            } else {
                $this->jsonObj->{$key} = $v;
            }
        }

        return $this->jsonObj;
    }

    protected function jsonSerializeRelatedObj($key, $o)
    {
        if (!($o instanceof ArrayCollection) && method_exists($o, 'getID')) {
            $this->jsonObj->{$key . 'ID'} = $o->getID();
        }
    }
}
