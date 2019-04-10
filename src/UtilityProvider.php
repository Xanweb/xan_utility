<?php
namespace XanUtility;

use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\User\User;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use XanUtility\Editor\CkEditor\Editor;

class UtilityProvider extends ServiceProvider
{
    public function register()
    {
        if (!$this->app->bound(User::class)) {
            $this->app->singleton(User::class);
        }

        $classAliasList = ClassAliasList::getInstance();
        $classAliasList->registerMultiple([
            'MultilingualSection' => 'Concrete\Core\Multilingual\Page\Section\Section'
        ]);

        $aliases = [
            'user/current' => User::class,
            'database/connection' => 'Concrete\Core\Database\Connection\Connection',
            'http/request' => 'Concrete\Core\Http\Request',
            'excel/export' => Service\Excel\Export::class,
            'excel/import' => Service\Excel\Import::class,
        ];

        foreach ($aliases as $alias => $class) {
            $this->app->alias($class, $alias);
        }

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
