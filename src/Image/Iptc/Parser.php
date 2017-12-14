<?php
namespace XanUtility\Image\Iptc;

use XanUtility\Image\Iptc as IptcData;

class Parser
{
    /**
     * Parse image file and extract Iptc data.
     *
     * @param \Concrete\Core\Entity\File\Version $version
     *
     * @return \XanUtility\Image\Iptc|bool
     */
    public static function parse($version)
    {
        $info = array();
        $size = getimagesize(DIR_BASE . $version->getRelativePath(), $info);

        if (false == $size || !isset($info['APP13'])) {
            return false;
        }

        $iptc = iptcparse($info['APP13']);

        if (!is_array($iptc)) {
            return false;
        }

        return new IptcData($iptc);
    }
}
