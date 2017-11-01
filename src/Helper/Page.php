<?php
namespace XanUtility\Helper;

use Block;

class Page
{
    /**
     * @var \Page
     */
    private $page;

    public function __construct(\Page $page)
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
                    $block = $ab->getInstance();
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
        $blocks = [];
        $handlesCount = count($btHandles);

        $i = 0;
        foreach ($blockIDs as $row) {
            $ab = Block::getByID($row['bID'], $this->page, $row['arHandle']);
            if (is_object($ab) && in_array($ab->getBlockTypeHandle(), $btHandles)) {
                $blocks[$ab->getBlockTypeHandle()] = $ab->getInstance();
                ++$i;
                if ($handlesCount == $i) {
                    break;
                }
            }
        }

        return $blocks;
    }
}
