<?php
namespace XanUtility\Helper;

use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page as PageObject;

class Page
{
    /**
     * @var PageObject
     */
    private $page;

    public function __construct(PageObject $page)
    {
        $this->page = $page;
    }

    /**
     * @param string $btHandle
     *
     * @return \Concrete\Core\Block\BlockController
     */
    public function getBlock($btHandle)
    {
        $blockIDs = $this->page->getBlockIDs();
        $block = null;
        if (is_array($blockIDs)) {
            foreach ($blockIDs as $row) {
                $ab = Block::getByID($row['bID'], $this->page, $row['arHandle']);
                if (is_object($ab) && $ab->getBlockTypeHandle() == $btHandle) {
                    $block = $ab->getController();
                    break;
                }
            }
        }

        return $block;
    }

    /**
     * @param string[] $btHandles
     *
     * @return array
     */
    public function getBlocks(array $btHandles)
    {
        $blockIDs = $this->page->getBlockIDs();
        $handlesCount = count($btHandles);
        $blocks = [];

        $i = 0;
        foreach ($blockIDs as $row) {
            $ab = Block::getByID($row['bID'], $this->page, $row['arHandle']);
            if (is_object($ab) && !isset($blocks[$ab->getBlockTypeHandle()]) && in_array($ab->getBlockTypeHandle(), $btHandles)) {
                $blocks[$ab->getBlockTypeHandle()] = $ab->getController();
                ++$i;
                if ($handlesCount == $i) {
                    break;
                }
            }
        }

        return $blocks;
    }
}
