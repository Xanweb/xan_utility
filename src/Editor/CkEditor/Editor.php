<?php
namespace XanUtility\Editor\CkEditor;

use Concrete\Core\Editor\EditorInterface;

class Editor
{
    /**
     * @var EditorInterface
     */
    protected $editor;

    /**
     * Editor Options.
     *
     * @var array
     */
    protected $options = [];

    public function __construct(EditorInterface $editor)
    {
        $this->editor = $editor;
    }

    /**
     * Set Options to show for editor.
     *
     * @param array $options
     */
    public function setEditorOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @see \Concrete\Core\Editor\EditorInterface::outputBlockEditModeEditor()
     */
    public function outputBlockEditModeEditor($key, $content)
    {
        return $this->outputStandardEditor($key, $content);
    }

    /**
     * @see \Concrete\Core\Editor\EditorInterface::outputStandardEditor()
     */
    public function outputStandardEditor($key, $content)
    {
        $identifier = $this->editor->getIdentifier();
        $html = sprintf(
            '<textarea id="%s" style="display:none;" name="%s">%s</textarea>',
            $identifier,
            $key,
            $content
        );

        $jsFunc = $this->getEditorInitJSFunction();

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
    public function getEditorInitJSFunction()
    {
        $pluginManager = $this->editor->getPluginManager();
        if ($pluginManager->isSelected('sourcearea')) {
            $pluginManager->deselect('sourcedialog');
        }

        $options = $this->getEditorOptions();

        return $this->editor->getEditorInitJSFunction($options);
    }

    /**
     * List of options to show for editor.
     *
     * @return array
     */
    protected function getEditorOptions()
    {
        return $this->options;
    }
}
