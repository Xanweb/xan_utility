<?php
namespace XanUtility\Entity;

use XanUtility\App;
use Doctrine\Common\Collections\ArrayCollection;
use Core;

abstract class EntityBase implements \JsonSerializable
{
    /**
     * used for json serialisation.
     *
     * @var \stdClass
     */
    private $jsonObj;

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed    $id          The identifier
     * @param int      $lockMode    The lock mode
     * @param int|null $lockVersion The lock version
     *
     * @return static|null The entity instance or NULL if the entity can not be found
     */
    public static function getByID($id, $lockMode = null, $lockVersion = null)
    {
        return App::em()->getRepository(get_called_class())->find($id, $lockMode, $lockVersion);
    }

    public function save()
    {
        $em = $this->persist();
        $em->flush();
    }

    public function persist()
    {
        $em = App::em();
        $em->persist($this);

        return $em;
    }

    public function delete($flush = true)
    {
        $em = App::em();
        $em->remove($this);
        if ($flush) {
            $em->flush();
        }
    }

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
        $dh = Core::getFacadeApplication()->make('date');
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

    private function jsonSerializeRelatedObj($key, $o)
    {
        if (!($o instanceof ArrayCollection) && method_exists($o, 'getID')) {
            $this->jsonObj->{$key . 'ID'} = $o->getID();
        }
    }
}
