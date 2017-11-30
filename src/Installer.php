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

class Installer
{
    
    /**
     * @param \Package $pkg
     */
    private $pkg;
    
    
    public function __construct($pkg)
    {
        $this->pkg = $pkg;
    }
    
    /**
     * @return \Package
     */
    private function getPackage()
    {
        return $this->pkg;
    }

    /**
     * install Page Template if not Exist.
     *
     * @param $pTemplateHandle
     * @param $pTemplateName
     */
    public function installPageTemplate($pTemplateHandle, $pTemplateName)
    {
        if (!PageTemplate::getByHandle($pTemplateHandle)) {
            PageTemplate::add($pTemplateHandle, $pTemplateName, 'blank.png', $this->getPackage());
        }
    }

    /**
     * Install Page Type if not Exist.
     *
     * @param $pTemplateHandle
     * @param $pTypeHandle
     * @param $pTypeName
     */
    public function installPageType($pTemplateHandle, $pTypeHandle, $pTypeName)
    {
        $pTPL = PageTemplate::getByHandle($pTemplateHandle);
        if (is_object($pTPL) && !PageType::getByHandle($pTypeHandle)) {
            PageType::add([
                'handle' => $pTypeHandle,
                'name' => $pTypeName,
                'defaultTemplate' => $pTPL,
                'ptIsFrequentlyAdded' => 1,
                'ptLaunchInComposer' => 1,
            ], $this->getPackage());
        }
    }

    /**
     * Intall Or Update single pages.
     *
     * @param array $paths array of paths and names
     * Example: 
     * <pre>
     * [
     *  ['pagePath', 'pageName', optionalArrayofAttributeKeysAndValues],
     * ]
     * </pre>
     */
    public function installSinglePages(array $paths)
    {
        foreach ($paths as $path) {
            $this->installSinglePage($path[0], $path[1], isset($path[2]) ? $path[2] : []);
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
    public function installSinglePage($path, $name, $options = [])
    {
        $sp = Page::getByPath($path);
        if (!is_object($sp) || COLLECTION_NOT_FOUND === $sp->getError()) {
            $sp = SinglePage::add($path, $this->getPackage());
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
    public function installBlockTypeSets(array $handles)
    {
        foreach ($handles as $handle) {
            $this->installBlockTypeSet($handle[0], $handle[1]);
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
    public function installBlockTypeSet($handle, $name)
    {
        $bts = BlockTypeSet::getByHandle($handle);

        if (!is_object($bts)) {
            $bts = BlockTypeSet::add($handle, $name, $this->getPackage());
        }

        return $bts;
    }

    /**
     * Intall Or Update BlockTypes.
     *
     * @param array $handles array of handles. You can also include Blocktype sets and
     *  use an array ['bt_handle', $btSetObj] instead of simple handle
     */
    public function installBlockTypes(array $handles)
    {
        foreach ($handles as $handle) {
            $blockTypeSet = null;
            $btHandle = $handle;
            if (is_array($handle)) {
                $blockTypeSet = isset($handle[1]) ? $handle[1] : null;
                $btHandle = $handle[0];
            }
            $this->installBlockType($btHandle, $blockTypeSet);
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
    public function installBlockType($handle, BlockTypeSet $bts = null)
    {
        $bt = BlockType::getByHandle($handle);

        if (!is_object($bt)) {
            $bt = BlockType::installBlockType($handle, $this->getPackage());
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
    public function installAttributeKeyCategory($handle, $allowSets = 0, array $associatedAttrTypes = [])
    {
        $app = Facade::getFacadeApplication();
        $akCategSvc = $app->make(CategoryService::class);
        $akCateg = $akCategSvc->getByHandle($handle);
        if (!is_object($akCateg)) {
            $akCateg = $akCategSvc->add($handle, $allowSets, $this->getPackage());
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
    public function installAttributeTypes(array $handles)
    {
        foreach ($handles as $handle) {
            $this->installAttributeType($handle[0], $handle[1], isset($handle[2]) ? $handle[2] : null);
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
    public function installAttributeType($handle, $name, $akc = null)
    {
        $app = Facade::getFacadeApplication();
        $atFactory = $app->make(TypeFactory::class);

        $at = $atFactory->getByHandle($handle);
        if (!is_object($at)) {
            $at = $atFactory->add($handle, $name, $this->getPackage());
        }

        if (is_object($akc)) {
            $akc->getController()->associateAttributeKeyType($at);
        }

        return $at;
    }
    
    /**
     * Intall SiteAttributeKeys.
     * Example of $data:
     * <pre>
     * [
     *    'at_type_handle' => [ 
     *       ['akHandle' => 'ak_handle', 'akName' => 'AttributeKey Name']
     *    ]
     * ]
     * </pre>
     * @param array $data array of handles and names
     */
    public function installSiteAttributeKeys(array $data)
    {
        return $this->installAttributeKeys('site', $data);
    }

    /**
     * Intall PageAttributeKeys.
     * Example of $data:
     * <pre>
     * [
     *    'at_type_handle' => [ 
     *       ['akHandle' => 'ak_handle', 'akName' => 'AttributeKey Name']
     *    ]
     * ]
     * </pre>
     * @param array $data array of handles and names
     */
    public function installPageAttributeKeys(array $data)
    {
        return $this->installAttributeKeys('collection', $data);
    }

    /**
     * Intall UserAttributeKeys.
     * Example of $data:
     * <pre>
     * [
     *    'at_type_handle' => [ 
     *       ['akHandle' => 'ak_handle', 'akName' => 'AttributeKey Name']
     *    ]
     * ]
     * </pre>
     * @param array $data array of handles and names
     */
    public function installUserAttributeKeys(array $data)
    {
        return $this->installAttributeKeys('user', $data);
    }

    /**
     * Intall FileAttributeKeys.
     * Example of $data:
     * <pre>
     * [
     *    'at_type_handle' => [ 
     *       ['akHandle' => 'ak_handle', 'akName' => 'AttributeKey Name']
     *    ]
     * ]
     * </pre>
     * @param array $data array of handles and names
     */
    public function installFileAttributeKeys(array $data)
    {
        return $this->installAttributeKeys('file', $data);
    }

    /**
     * Intall AttributeKeys.
     *
     * @param \Concrete\Core\Attribute\Key\Category|string $akCateg AttributeKeyCategory object or handle
     * @param array $data array of handles and names
     */
    public function installAttributeKeys($akCateg, array $data)
    {
        $app = Facade::getFacadeApplication();
        $atFactory = $app->make(TypeFactory::class);

        if (is_string($akCateg)) {
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        foreach ($data as $atHandle => $attrs) {
            $at = $atFactory->getByHandle($atHandle);
            foreach ($attrs as $params) {
                $this->installAttributeKey($akCateg, $at, $params);
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
    public function installAttributeKey($akCateg, $atTypeHandle, $data)
    {
        if (is_string($akCateg)) {
            $app = Facade::getFacadeApplication();
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        $cak = $akCateg->getAttributeKeyByHandle($data['akHandle']);
        if (!is_object($cak)) {
            return $akCateg->add($atTypeHandle, $data, false, $this->getPackage());
        }

        return $cak;
    }

    /**
     * Intall PageAttributeSets.
     *
     * @param array $data array of handles and names
     */
    public function installPageAttributeSets(array $data)
    {
        return $this->installAttributeSets('collection', $data);
    }

    /**
     * Intall UserAttributeSets.
     *
     * @param array $data array of handles and names
     */
    public function installUserAttributeSets(array $data)
    {
        return $this->installAttributeSets('user', $data);
    }

    /**
     * Intall FileAttributeSets.
     *
     * @param array $data array of handles and names
     */
    public function installFileAttributeSets(array $data)
    {
        return $this->installAttributeSets('file', $data);
    }

    /**
     * Intall AttributeSets.
     *
     * @param \Concrete\Core\Attribute\Category\AbstractStandardCategory $akCateg AttributeKeyCategory object or handle
     * @param array $data array of handles and names
     */
    public function installAttributeSets($akCateg, array $data)
    {
        if (is_string($akCateg)) {
            $app = Facade::getFacadeApplication();
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        foreach ($data as $params) {
            $this->installAttributeSet($akCateg, $params[0], $params[1], isset($params[2]) ? $params[2] : []);
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
    public function installAttributeSet($akCateg, $handle, $name, array $associatedAttrs = [])
    {
        if (is_string($akCateg)) {
            $app = Facade::getFacadeApplication();
            $akCateg = $app->make(CategoryService::class)->getByHandle($akCateg)->getController();
        }

        $manager = $akCateg->getSetManager();
        $factory = new SetFactory($akCateg->getEntityManager());
        $set = $factory->getByHandle($handle);
        if (!is_object($set)) {
            $set = $manager->addSet($handle, $name, $this->getPackage());
        }

        foreach ($associatedAttrs as $akHandle) {
            $cak = $akCateg->getAttributeKeyByHandle($akHandle);
            $manager->addKey($set, $cak);
        }

        return $set;
    }
}
