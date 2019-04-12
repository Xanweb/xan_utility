<?php
namespace XanUtility\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use XanUtility\Block\Migrator\PrimitiveField as PrimitiveFieldMigrator;

trait BlockControllerTrait
{
    protected function getImportData($blockNode, $page)
    {
        $args = [];
        $inspector = $this->app->make('import/value_inspector');
        if (isset($blockNode->data)) {
            $db = $this->app->make('database/connection'); /* @var \Concrete\Core\Database\Connection\Connection $db */
            $schManager = $db->getSchemaManager();
            foreach ($blockNode->data as $data) {
                $table = $this->getDBTableMapping((string) $data['table']);
                if ($table == $this->getBlockTypeDatabaseTable()) {
                    if (isset($data->record)) {
                        $columns = [];
                        foreach ($schManager->listTableColumns($table) as $column) {
                            $columns[$column->getName()] = $column;
                        }

                        foreach ($data->record->children() as $node) {
                            $fieldName = $this->getDBTableFieldMapping((string) $data['table'], $node->getName());
                            if ($fieldName) {
                                $migrator = false;
                                if (is_array($fieldName)) {
                                    list($fieldName, $migrator) = $fieldName;
                                }

                                $result = $inspector->inspect((string) $node);
                                $args[$fieldName] = $migrator ? $this->app->call($migrator, [$this->app, $result->getReplacedValue()]) : $result->getReplacedValue();

                                if (isset($columns[$fieldName])) {
                                    $args[$fieldName] = PrimitiveFieldMigrator::sanitizeFieldValue($columns[$fieldName]->getType()->getName(), $args[$fieldName]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $args;
    }

    protected function importAdditionalData($b, $blockNode)
    {
        $inspector = $this->app->make('import/value_inspector');
        if (isset($blockNode->data)) {
            $db = $this->app->make('database/connection'); /* @var \Concrete\Core\Database\Connection\Connection $db */
            $schManager = $db->getSchemaManager();
            foreach ($blockNode->data as $data) {
                $table = $this->getDBTableMapping((string) $data['table']);
                if (strtoupper($table) != strtoupper($this->getBlockTypeDatabaseTable())) {
                    if (isset($data->record)) {
                        $columns = [];
                        foreach ($schManager->listTableColumns($table) as $column) {
                            $columns[$column->getName()] = $column;
                        }
                        foreach ($data->record as $record) {
                            $aar = new \Concrete\Core\Legacy\BlockRecord($table);
                            $aar->bID = $b->getBlockID();
                            foreach ($record->children() as $node) {
                                $fieldName = $this->getDBTableFieldMapping((string) $data['table'], $node->getName());

                                if (!$fieldName) {
                                    continue;
                                }

                                $migrator = false;
                                if (is_array($fieldName)) {
                                    list($fieldName, $migrator) = $fieldName;
                                }

                                $result = $inspector->inspect((string) $node);
                                $aar->{$fieldName} = $migrator ? $this->app->call($migrator, [$this->app, $result->getReplacedValue()]) : $result->getReplacedValue();

                                if (isset($columns[$fieldName])) {
                                    $aar->{$fieldName} = PrimitiveFieldMigrator::sanitizeFieldValue($columns[$fieldName]->getType()->getName(), $aar->{$fieldName});
                                }
                            }
                            $aar->Save();
                        }
                    }
                }
            }
        }
    }

    protected function getDBTableMapping($table)
    {
        return $this->app['config']->get("mapping.{$this->btHandle}.{$table}.table", $table);
    }

    protected function getDBTableFieldMapping($table, $fieldName)
    {
        return $this->app['config']->get("mapping.{$this->btHandle}.{$table}.fields.{$fieldName}", $fieldName);
    }

    /**
     * return current block area.
     *
     * @return string
     */
    protected function getCurrentAreaName()
    {
        if ($this->block instanceof Block) {
            $areaName = $this->block->getAreaHandle();
        } else {
            $areaName = $this->request->request('arHandle');
        }

        return $areaName;
    }

    /**
     * Check if active user can edit block.
     *
     * @return bool
     */
    public function userCanEditBlock()
    {
        if (!is_object($this->block)) {
            return false;
        }

        $bp = new \Permissions($this->block);

        return $bp->canWrite();
    }

    /**
     * Check whether we are in Edit Mode.
     *
     * @return bool
     */
    public function isPageInEditMode()
    {
        $c = $this->getCurrentPage();

        if ($c instanceof Page && !$c->isError()) {
            return $c->isEditMode();
        }

        return false;
    }

    /**
     * Get Active Block Page Language.
     *
     * @return string
     */
    public function getCurrentPageLang()
    {
        static $lang;

        if (!$lang) {
            $collection = $this->getCurrentPage();

            if ($collection instanceof Page) {
                $section = Section::getBySectionOfSite($collection);
                if (is_object($section)) {
                    $lang = $section->getLanguage();
                }
            }

            if (!$lang) {
                $lang = Localization::activeLanguage();
            }
        }

        return $lang;
    }

    /**
     * Get Active Page.
     *
     * @return Page
     */
    public function getCurrentPage()
    {
        $collection = $this->getCollectionObject();

        if ($this->isEditedWithinStack()) {
            return $collection;
        }

        $c = Page::getCurrentPage();
        if ($c instanceof Page && !$c->isError() && !$c->isMasterCollection()) {
            return $c;
        }

        return $collection;
    }

    /**
     * Check if the block is edited in Stack.
     *
     * @return bool
     */
    public function isEditedWithinStack()
    {
        $path = $this->request->getPath();
        if (!empty($path) && $this->request->matches('*' . STACKS_LISTING_PAGE_PATH . '*')) {
            $cID = (string) end(explode('/', $path));
            if (false !== strpos($cID, '@')) {
                list($cID, $locale) = explode('@', $cID, 2);
            }

            if ($cID > 0) {
                $s = Stack::getByID($cID);

                return is_object($s);
            }
        }

        return false;
    }
}
