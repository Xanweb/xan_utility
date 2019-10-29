<?php
namespace XanUtility\PageList;

use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Foundation\Service\Provider as BaseServiceProvider;
use XanUtility\PageList\Fetcher\AttributePropertyFetcher;
use XanUtility\PageList\Fetcher\BlockPropertyFetcher;
use XanUtility\PageList\Fetcher\PagePropertyFetcher;
use Doctrine\ORM\EntityManagerInterface;

class Provider extends BaseServiceProvider
{
    /**
     * Set a common namespace for all classes
     */
    private $namespace = __NAMESPACE__;

    public function register()
    {
        $app = $this->app;

        $this->app->singleton('page_info/config/default', function () use ($app) {
            $config = $app->make(PageInfoConfig::class);
            $config->registerPageNameFetcher(new PagePropertyFetcher(PagePropertyFetcher::PAGE_NAME));
            $config->registerPageDescriptionFetcher(new PagePropertyFetcher(PagePropertyFetcher::PAGE_DESCRIPTION));
            $config->registerThumbnailFetcher(new AttributePropertyFetcher('thumbnail'));

            return $config;
        });

        $this->app->singleton('page_info/config/basic', function () use ($app) {
            $config = $app->make(PageInfoConfig::class);
            $config->registerPageNameFetcher(new PagePropertyFetcher(PagePropertyFetcher::PAGE_NAME));
            $config->registerPageDescriptionFetcher(new PagePropertyFetcher(PagePropertyFetcher::PAGE_DESCRIPTION));
            $config->registerThumbnailFetcher(new AttributePropertyFetcher('thumbnail'));

            $repo = $app->make(EntityManagerInterface::class)->getRepository(BlockType::class);
            $btXanImage = $repo->findOneBy(['btHandle' => 'xan_image']);
            $config->registerThumbnailFetcher(new BlockPropertyFetcher(
                is_object($btXanImage) ? 'xan_image' : 'image', function ($bController) {
                    return $bController->getFileObject();
                }
            ));

            return $config;
        });

        $this->app->singleton('page_info/config/advanced', function () use ($app) {
            $config = $app->make(PageInfoConfig::class);
            $repo = $app->make(EntityManagerInterface::class)->getRepository(BlockType::class);
            $pageHeadingBlock = $repo->findOneBy(['btHandle' => 'page_heading']);
            if (is_object($pageHeadingBlock)) {
                $config->registerPageNameFetcher(new BlockPropertyFetcher(
                    'page_heading', function ($bController) {
                        return $bController->getPageHeading();
                    }
                ));

                $config->registerPageDescriptionFetcher(new BlockPropertyFetcher(
                    'page_heading', function ($bController) {
                        return $bController->getTeaserText();
                    }
                ));
            } else {
                $config->registerPageNameFetcher(new BlockPropertyFetcher(
                    'page_title', function ($bController) {
                        return $bController->getTitleText();
                    }
                ));
            }

            $config->registerPageNameFetcher(new PagePropertyFetcher(PagePropertyFetcher::PAGE_NAME));
            $config->registerPageDescriptionFetcher(new PagePropertyFetcher(PagePropertyFetcher::PAGE_DESCRIPTION));
            $config->registerThumbnailFetcher(new AttributePropertyFetcher('thumbnail'));

            $btXanImage = $repo->findOneBy(['btHandle' => 'xan_image']);
            $config->registerThumbnailFetcher(new BlockPropertyFetcher(
                is_object($btXanImage) ? 'xan_image' : 'image', function ($bController) {
                    return $bController->getFileObject();
                }
            ));

            return $config;
        });

        $this->app->singleton([PageInfoFactory::class => 'pl/page_info/factory']);
    }
}
