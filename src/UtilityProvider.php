<?php
namespace XanUtility;

use Concrete\Core\User\User;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use XanUtility\Console\Command\TranslatePackageExceptCoreTranslationsCommand;
use XanUtility\Editor\CkEditor\Editor;

class UtilityProvider extends ServiceProvider
{
    public function register()
    {
        if (!$this->app->bound(User::class)) {
            $this->app->singleton(User::class);
        }

        $aliases = [
            'user/current' => User::class,
            'http/request' => 'Concrete\Core\Http\Request',
            'excel/export' => 'XanUtility\Service\Excel\Service\Excel\Export',
            'excel/import' => 'XanUtility\Service\Excel\Service\Excel\Import',
            'database/connection' => 'Concrete\Core\Database\Connection\Connection',
        ];

        foreach ($aliases as $alias => $class) {
            $this->app->alias($class, $alias);
        }

        $this->app->bind('site/active', function (Application $app) {
            return $app->make('site')->getSite();
        });

        $this->app->singleton(['XanUtility\Html\Image' => 'xan/html/image']);

        $app = $this->app;
        $this->app['director']->addListener('on_before_console_run', function () use ($app) {
            $app['console']->add($app->make(TranslatePackageExceptCoreTranslationsCommand::class));
        });

        $this->app->singleton('editor/compact', function (Application $app) {
            $editor = $app->make(Editor::class);
            $editor->setEditorOptions([
                'disableAutoInline' => true,
                'toolbarGroups' => [
                    ['name' => 'basicstyles', 'groups' => ['basicstyles']],
                    ['name' => 'list', 'groups' => ['list']],
                    ['name' => 'indent', 'groups' => ['indent']],
                    ['name' => 'align', 'groups' => ['align']],
                    ['name' => 'links', 'groups' => ['links']],
                    ['name' => 'styles', 'groups' => ['styles']],
                ],
                'removeButtons' => 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
            ]);

            return $editor;
        });

        $this->app->singleton('editor/smart', function (Application $app) {
            $editor = $app->make(Editor::class);
            $editor->setEditorOptions([
                'disableAutoInline' => true,
                'toolbarGroups' => [
                    ['name' => 'undo', 'groups' => ['undo']],
                    ['name' => 'basicstyles', 'groups' => ['basicstyles']],
                ],
                'removeButtons' => 'Underline,Strike,Subscript,Superscript',
            ]);

            return $editor;
        });
    }
}
