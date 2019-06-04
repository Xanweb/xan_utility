<?php
namespace XanUtility\Migration\Import;

interface PagePathMapperInterface
{

    /**
     * Get Mapped Path for SiteTree from C5.6 Path
     *
     * @param string $path
     *
     * @return array [$mappedPath, $siteTree]
     */
    public function getMappedPath($path);
}