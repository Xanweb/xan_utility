<?php

namespace XanUtility\Helper;

use Concrete\Core\View\View;

class OgTags
{
    public static function output(array $data)
    {
        $tags = ['title', 'type', 'url', 'description', 'image', 'site_name'];
        $defaultValues = ['type' => 'article'];
        $output = '';

        // add defult values
        foreach ($defaultValues as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = $value;
            }
        }

        // add tags
        foreach ($data as $key => $value) {
            if (in_array($key, $tags)) {
                $output .= '<meta property="og:' . $key . '" content="' . h($value) . '" />';
            }
        }

        // add tags to header
        $view = View::getInstance();
        $view->addHeaderItem($output);
    }
}