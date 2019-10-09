<?php
namespace XanUtility\Mail;

use Concrete\Core\Entity\Package;

class HtmlTemplate
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct($htmlFileName, Package $pkg = null)
    {
        $filePath = implode(DIRECTORY_SEPARATOR, [
            is_object($pkg) ? $pkg->getPackagePath() : DIR_APPLICATION,
            DIRNAME_MAIL_TEMPLATES,
            'html',
            $htmlFileName,
        ]);

        if (!file_exists($filePath)) {
            throw new \Exception(t('Template File %s not found at path "%s".', $htmlFileName, $filePath));
        }

        $this->filePath = $filePath;
    }

    /**
     * Render Mail Template.
     *
     * @param array $args
     *
     * @return string
     */
    public function render(array $args = [])
    {
        $htmlContent = file_get_contents($this->filePath);

        if (!empty($args)) {
            $placeHolders = array_map(function ($v) {
                return '<!--' . $v . '-->';
            }, array_keys($args));

            $htmlContent = str_replace($placeHolders, array_values($args), $htmlContent);
        }

        return $htmlContent;
    }
}
