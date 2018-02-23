<?php
namespace XanUtility\Mail\Service;

class Obfuscator
{

    private static function obfuscateMailLinks($groups)
    {
        $mid    = round(strlen($groups[2]) / 2);
        // make sure we don't split within something like &amp;
        $umlPos = strpos($groups[4], "uml;");
        $mid2   = $umlPos !== false ? $umlPos + 4 : round(strlen($groups[4]) / 2);
        return $ret    = '<a '.$groups[1].' href="#" onclick="location.href=\'mailto:'.substr($groups[2],
                0, $mid).'\'+\''.substr($groups[2], $mid).'\'" '.$groups[3].'>'.substr($groups[4],
                0, $mid2).'<span style="display:none;"></span>'.substr($groups[4], $mid2).'</a>';
    }

    public static function obfuscateMail($email)
    {
        $ret = "";
        for ($i = 0; $i < strlen($email); $i++) {
            $ret .= "&#".ord($email[$i]).";";
        }
        return $ret;
    }

    public static function obfuscate($content)
    {
        // do not encrypt mail address in case we're rendering the site
        // on the server, this is helpful because we can make sure
        // that our newsletter tool sees the mail addresses the way they are
        if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            $content = preg_replace_callback('#\<a(.*)href="mailto:(.*)"(.*)>(.*)</a>#Ui', array(static::class, 'obfuscateMailLinks'), $content);
        }

        return $content;
    }
}