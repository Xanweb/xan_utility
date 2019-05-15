<?php
namespace XanUtility\Route;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{

    public function loadRoutes(Router $router)
    {
        $router->buildGroup()
            ->setNamespace('XanUtility\Controller\Frontend')
            ->routes(function (Router $r) {
                $r->get('/js/xan/utility/global.js', 'XanBase::getJavascript');
            });
    }
}