<?php
namespace XanUtility\Controller\Frontend;

use Concrete\Core\Http\ResponseFactoryInterface;
use Controller;
use PageTheme;

class XanBase extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getJavascript()
    {
        $content = 'window.ccm_xan = ' . json_encode([
            'themePath' => $this->getThemeRelativePath(),
            'i18n' => [
                'confirm' => t('Are you sure?'),
                'maxItemsExceeded' => t('Max items exceeded, You can not add any more items.'),
                'pageNotFound' => t('Page not found'),
            ],
            'editor' => [
                'initCompactEditor' => '###initCompactEditor###',
            ],
        ], JSON_UNESCAPED_SLASHES) . ';';

        $content = str_replace('"###initCompactEditor###"', $this->app['editor/compact']->getEditorInitJSFunction(), $content);

        return $this->createJavascriptResponse($content);
    }

    /**
     * gets the relative theme path for use in templates.
     *
     * @return string $themePath
     */
    private function getThemeRelativePath()
    {
        $theme = PageTheme::getSiteTheme();

        return $this->app->make('environment')->getURL(DIRNAME_THEMES . '/' . $theme->getThemeHandle(), $theme->getPackageHandle());
    }

    /**
     * @param string $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createJavascriptResponse($content)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        $response = $rf->create(
            $content,
            200,
            [
                'Content-Type' => 'application/javascript; charset=' . APP_CHARSET,
                'Content-Length' => strlen($content),
            ]
        );

        return $response;
    }
}
