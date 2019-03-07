<?php
namespace XanUtility\Form\Service\Widget;

use Concrete\Core\Http\Request;
use XanUtility\Application\ApplicationTrait;
use Concrete\Core\Form\Service\Widget\PageSelector as CorePageSelector;

class PageSelector extends CorePageSelector
{
    use ApplicationTrait;

    /**
     * Creates form fields and JavaScript page chooser for choosing a page. For use with inclusion in blocks.
     * <code>
     *     $dh->selectPage('pageID', '1', 'ccm_SelectPage'); // prints out the home page and makes it selectable.
     * </code>.
     *
     * @param $fieldName
     * @param bool|int $cID
     * @param string $callbackJsFunc
     *
     * @return string
     */
    public function selectPage($fieldName, $cID = false, $callbackJsFunc = null)
    {
        $v = \View::getInstance();
        $v->requireAsset('xan/sitemap');

        $r = $this->app()->make(Request::class);
        $selectedCID = (int) $r->get($fieldName, $cID);

        $args = "{'inputName': '{$fieldName}'";
        if ($selectedCID) {
            $args .= ", 'cID': {$selectedCID}";
        }

        if (!empty($callbackJsFunc)) {
            $args .= ", 'onChange': {$callbackJsFunc}";
        }

        $args .= '}';

        $identifier = $this->app('helper/validation/identifier')->getString(32);
        $html = <<<EOL
        <div data-page-selector="{$identifier}"></div>
        <script type="text/javascript">
        $(function() {
            $('[data-page-selector={$identifier}]').xanPageSelector({$args});
        })
        </script>
EOL;

        return $html;
    }
}
