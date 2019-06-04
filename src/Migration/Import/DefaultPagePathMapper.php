<?php
namespace XanUtility\Migration\Import;

use Concrete\Core\Application\Application;

class DefaultPagePathMapper implements PagePathMapperInterface
{
    /**
     * @var array ["lang" => $siteTreeObject]
     */
    private $siteTrees = [];

    /**
     * @var string
     */
    private $defaultLanguage;

    public function __construct(Application $app)
    {
        $site = $app->make('site')->getSite();
        foreach ($site->getLocales() as $locale) {
            $this->siteTrees[$locale->getLanguage()] = $locale->getSiteTreeObject();
        }

        $this->defaultLanguage = $site->getDefaultLocale()->getLanguage();;
    }

    /**
     * {@inheritDoc}
     */
    public function getMappedPath($path)
    {
        if(count($this->siteTrees) === 1) {
            return [$path, head($this->siteTrees)];
        }

        $mappedPath = $path;
        $extractedPath = explode('/', trim($path, '/'));
        $lang = $extractedPath[0];

        if($lang == $this->defaultLanguage) {
            // Remove /lang part
            $mappedPath = '/' . implode('/', array_slice($extractedPath, 1));
        }

        return [$mappedPath, $this->siteTrees[$lang]];
    }
}