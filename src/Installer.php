<?php
namespace XanUtility;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Support\Facade\Facade;
use PageTemplate;
use PageType;

abstract class Installer
{
    public static function getAppClassName()
    {
        throw new \Exception('Please Override Installer Class and implement getAppClassName method');
    }

    /**
     * @return \Package
     */
    private static function getPackage()
    {
        return call_user_func([static::getAppClassName(), 'pkg']);
    }

    /**
     * install Page Template if not Exist.
     *
     * @param $pTemplateHandle
     * @param $pTemplateName
     */
    public static function installPageTemplate($pTemplateHandle, $pTemplateName)
    {
        if (!PageTemplate::getByHandle($pTemplateHandle)) {
            PageTemplate::add($pTemplateHandle, $pTemplateName, 'blank.png', static::getPackage());
        }
    }

    /**
     * Install Page Type if not Exist.
     *
     * @param $pTemplateHandle
     * @param $pTypeHandle
     * @param $pTypeName
     */
    public static function installPageType($pTemplateHandle, $pTypeHandle, $pTypeName)
    {
        $pTPL = PageTemplate::getByHandle($pTemplateHandle);
        if (is_object($pTPL) && !PageType::getByHandle($pTypeHandle)) {
            PageType::add([
                'handle' => $pTypeHandle,
                'name' => $pTypeName,
                'defaultTemplate' => $pTPL,
                'ptIsFrequentlyAdded' => 1,
                'ptLaunchInComposer' => 1,
            ], static::getPackage());
        }
    }

    /**
     * Intall Or Update single pages.
     *
     * @param array $paths array of paths and names
     */
    public static function installSinglePages(array $paths)
    {
        foreach ($paths as $path) {
            static::installSinglePage($path[0], $path[1], isset($path[2]) ? $path[2] : []);
        }
    }

    /**
     * Intall Or Update Single Page if Exists.
     *
     * @param string $path
     * @param string $name
     * @param array $options
     *
     * @return SinglePage return installed single page
     */
    public static function installSinglePage($path, $name, $options = [])
    {
        $sp = Page::getByPath($path);
        if (!is_object($sp) || COLLECTION_NOT_FOUND === $sp->getError()) {
            $sp = SinglePage::add($path, static::getPackage());
            $sp->update([
                'cName' => $name,
            ]);
        }

        foreach ($options as $key => $value) {
            $sp->setAttribute($key, $value);
        }

        return $sp;
    }

    /**
     * Intall Or Update BlockTypeSets.
     *
     * @param array $handles array of handles and names
     */
    public static function installBlockTypeSets(array $handles)
    {
        foreach ($handles as $handle) {
            static::installBlockTypeSet($handle[0], $handle[1]);
        }
    }

    /**
     * Intall Or Update BlockTypeSet if Exists.
     *
     * @param string $handle
     * @param string $name
     *
     * @return BlockTypeSet return installed BlockTypeSet
     */
    public static function installBlockTypeSet($handle, $name)
    {
        $bts = BlockTypeSet::getByHandle($handle);

        if (!is_object($bts)) {
            $bts = BlockTypeSet::add($handle, $name, static::getPackage());
        }

        return $bts;
    }

    /**
     * Intall Or Update BlockTypes.
     *
     * @param array $handles array of handles and BlockTypeSet $bts
     */
    public static function installBlockTypes(array $handles)
    {
        foreach ($handles as $handle) {
            $blockTypeSet = null;
            $btHandle = $handle;
            if (is_array($handle)) {
                $blockTypeSet = isset($handle[1]) ? $handle[1] : null;
                $btHandle = $handle[0];
            }
            static::installBlockType($btHandle, $blockTypeSet);
        }
    }

    /**
     * Intall Or Update BlockType if Exists.
     *
     * @param string $handle
     * @param BlockTypeSet $bts
     *
     * @return BlockType return installed BlockType
     */
    public static function installBlockType($handle, BlockTypeSet $bts = null)
    {
        $bt = BlockType::getByHandle($handle);

        if (!is_object($bt)) {
            $bt = BlockType::installBlockType($handle, static::getPackage());
        }

        if (is_object($bts)) {
            $bts->addBlockType($bt);
        }

        return $bt;
    }

    /**
     * Intall Or Update AttributeKeyCategory.
     *
     * @param string $handle The handle string for the category
     * @param int $allowSets This should be an attribute AttributeKeyCategory::ASET_ALLOW_* constant
     * @param array $associatedAttrTypes array of attribute type handles to be associated with
     *
     * @return Concrete\Core\Entity\Attribute\Category
     */
    public static function installAttributeKeyCategory($handle, $allowSets = 0, array $associatedAttrTypes = [])
    {
        $app = Facade::getFacadeApplication();
        $akCategSvc = $app->make(CategoryService::class);
        $akCateg = $akCategSvc->getByHandle($handle);
        if (!is_object($akCateg)) {
            $akCateg = $akCategSvc->add($handle, $allowSets, static::getPackage());
        }

        $atFactory = $app->make(TypeFactory::class);
        foreach ($associatedAttrTypes as $atHandle) {
            $akCateg->associateAttributeKeyType($atFactory->getByHandle($atHandle));
        }

        return $akCateg;
    }

    /**
     * Intall Or Update AttributeTypes.
     *
     * @param array $handles array of handles and names
     */
    public static function installAttributeTypes(array $handles)
    {
        foreach ($handles as $handle) {
            static::installAttributeType($handle[0], $handle[1], isset($handle[2]) ? $handle[2] : null);
        }
    }

    /**
     * Intall Or Update AttributeType if Exists.
     *
     * @param string $handle
     * @param string $name
     * @param \Concrete\Core\Entity\Attribute\Category $akc
     *
     * @return \Concrete\Core\Entity\Attribute\Type return installed attribute type
     */
    public static function installAttributeType($handle, $name, $akc = null)
    {
        $app = Facade::getFacadeApplication();
        $atFactory = $app->make(TypeFactory::class);

        $at = $atFactory->getByHandle($handle);
        if (!is_object($at)) {
            $at = $atFactory->add($handle, $name, static::getPackage());
        }

        if (is_object($akc)) {
            $akc->getController()->associateAttributeKeyType($at);
        }

        return $at;
    }

    /**
     * Intall PageAttributeKeys.
     *
     * @param array $data array of handles and names
     */
    public static function installPageAttributeKeys(array $data)
    {
        return static::installAttributeKeys('collection', $data);
    }

    /**
     * Intall UserAttributeKeys.
     *
     * @param array $data array of handles and names
     */
    public static function installUserAttributeKeys(array $data)
    {
        return static::installAttributeKeys('user', $data);
    }

    /**
     * Intall FileAttributeKeys.
     *
     * @param array $data array of handles and names
     */
    public static function installFileAttributeKeys(array $data)
    {
        return static::installAttributeKeys('file', $data);
    }

    /**
     * Intall AttributeKeys.
     *
     * @param \Concrete\Core\Attribute\Key\Category|string $akCateg AttributeKeyCategory object or handle
     * @param array $data array of handles and names
     */
    public static function installAttributeKeys($akCateg, array $data)
    {
        $app = Facade::getFacadeApplication();
        $atFactory = $app->make(TypeFactory::class);

        if (is_string($akCateg)) {
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        foreach ($data as $atHandle => $attrs) {
            $at = $atFactory->getByHandle($atHandle);
            foreach ($attrs as $params) {
                static::installAttributeKey($akCateg, $at, $params);
            }
        }
    }

    /**
     * Intall CollectionAttributeKey if not Exists.
     *
     * @param \Concrete\Core\Attribute\Category\AbstractStandardCategory $akCateg AttributeKeyCategory object or handle
     * @param string $atTypeHandle
     * @param array $data
     *
     * @return CollectionAttributeKey; return installed attribute key
     */
    public static function installAttributeKey($akCateg, $atTypeHandle, $data)
    {
        if (is_string($akCateg)) {
            $app = Facade::getFacadeApplication();
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        $cak = $akCateg->getAttributeKeyByHandle($data['akHandle']);
        if (!is_object($cak)) {
            return $akCateg->add($atTypeHandle, $data, false, static::getPackage());
        }

        return $cak;
    }

    /**
     * Intall PageAttributeSets.
     *
     * @param array $data array of handles and names
     */
    public static function installPageAttributeSets(array $data)
    {
        return static::installAttributeSets('collection', $data);
    }

    /**
     * Intall UserAttributeSets.
     *
     * @param array $data array of handles and names
     */
    public static function installUserAttributeSets(array $data)
    {
        return static::installAttributeSets('user', $data);
    }

    /**
     * Intall FileAttributeSets.
     *
     * @param array $data array of handles and names
     */
    public static function installFileAttributeSets(array $data)
    {
        return static::installAttributeSets('file', $data);
    }

    /**
     * Intall AttributeSets.
     *
     * @param \Concrete\Core\Attribute\Category\AbstractStandardCategory $akCateg AttributeKeyCategory object or handle
     * @param array $data array of handles and names
     */
    public static function installAttributeSets($akCateg, array $data)
    {
        if (is_string($akCateg)) {
            $app = Facade::getFacadeApplication();
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        foreach ($data as $params) {
            static::installAttributeSet($akCateg, $params[0], $params[1], isset($params[2]) ? $params[2] : []);
        }
    }

    /**
     * @param \Concrete\Core\Attribute\Category\AbstractStandardCategory $akCateg
     * @param string $handle
     * @param string $name
     * @param array $associatedAttrs
     *
     * @return \Concrete\Core\Entity\Attribute\Set
     */
    public static function installAttributeSet($akCateg, $handle, $name, array $associatedAttrs = [])
    {
        if (is_string($akCateg)) {
            $app = Facade::getFacadeApplication();
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        $manager = $akCateg->getSetManager();
        $factory = new SetFactory($akCateg->getEntityManager());
        $set = $factory->getByHandle($handle);
        if (!is_object($set)) {
            $set = $manager->addSet($handle, $name, static::getPackage());
        }

        foreach ($associatedAttrs as $akHandle) {
            $cak = $akCateg->getAttributeKeyByHandle($akHandle);
            $manager->addKey($set, $cak);
        }

        return $set;
    }
}
