<?php
namespace XanUtility\Editor\CkEditor;

class CompactEditor
{
    /**
     * Generate the HTML to be placed in a page to display the editor with compact options.
     *
     * @param string $key the name of the field to be used to POST the editor content
     * @param string $content The initial value of the editor content
     *
     * @return string
     */
    public static function outputEditor($key, $content)
    {
        /* @var $editor \Concrete\Core\Editor\CkeditorEditor */
        $editor = c5app()->make('editor');

        $identifier = $editor->getIdentifier();
        $html = sprintf(
            '<textarea id="%s" style="display:none;" name="%s">%s</textarea>',
            $identifier,
            $key,
            $content
        );

        $options = static::getEditorOptions();
        $jsFunc = $editor->getEditorInitJSFunction($options);

        $html .= <<<EOL
        <script>
        $(function() {
            var initEditor = {$jsFunc};
            initEditor('#{$identifier}');
         })
        </script>
EOL;

        return $html;
    }

    /**
     * Generate the compact Javascript code that initialize the editor.
     *
     * @return string
     */
    public static function outputEditorInitJSFunction()
    {
        /* @var $editor \Concrete\Core\Editor\CkeditorEditor */
        $editor = c5app()->make('editor');

        $pluginManager = $editor->getPluginManager();
        if ($pluginManager->isSelected('sourcearea')) {
            $pluginManager->deselect('sourcedialog');
        }

        $options = static::getEditorOptions();

        return $editor->getEditorInitJSFunction($options);
    }

    /**
     * List of options to show for editor.
     *
     * @return array
     */
    protected static function getEditorOptions()
    {
        return [
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
        ];
    }
}
