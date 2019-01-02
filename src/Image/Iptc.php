<?php
namespace XanUtility\Image;

/**
 * IPTC data.
 */
class Iptc
{
    const IPTC_CAPTION = '2#120';
    const IPTC_TITLE = '2#005';
    const IPTC_COPYRIGHT = '2#116';
    const IPTC_AUTHOR = '2#080';
    const IPTC_KEYWORDS = '2#025';
    const IPTC_CODE_CHARACTER_SET = '1#090';

    protected $caption;
    protected $title;
    protected $copyright;
    protected $author;
    protected $keywords;
    private $isUtf8Encoded = false;

    /**
     * constructor.
     *
     * @param array $data Attribute
     */
    public function __construct(array $data = [])
    {
        $this->setIsUtf8EncodedByData($data);
        $this->setAttributes($data);
    }

    /**
     * return the encoding form data.
     *
     * @return bool
     */
    public function isUtf8Encoded()
    {
        return $this->isUtf8Encoded;
    }

    /**
     * get caption.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * setCaption.
     *
     * @param string $caption caption
     */
    public function setCaption($caption)
    {
        if ($this->isUtf8Encoded()) {
            $caption = utf8_encode($caption);
        }
        $this->caption = $caption;
    }

    /**
     * get headline.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set headline.
     *
     * @param string $headline headline
     */
    public function setTitle($headline)
    {
        if ($this->isUtf8Encoded()) {
            $headline = utf8_encode($headline);
        }
        $this->title = $headline;
    }

    /**
     * get copyright.
     *
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * set copyright.
     *
     * @param string $copyright copyright
     */
    public function setCopyright($copyright)
    {
        if ($this->isUtf8Encoded()) {
            $copyright = utf8_encode($copyright);
        }
        $this->copyright = $copyright;
    }

    /**
     * get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * set author.
     *
     * @param string $author author
     */
    public function setAuthor($author)
    {
        if ($this->isUtf8Encoded()) {
            $author = utf8_encode($author);
        }
        $this->author = $author;
    }

    /**
     * get keywords.
     *
     * @return string keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * set keywords.
     *
     * @param string|array $keywords keywords
     */
    public function setKeywords($keywords)
    {
        if (is_array($keywords)) {
            foreach ($keywords as $keyword) {
                $this->addKeyword($keyword);
            }
        } else {
            $this->addKeyword($keywords);
        }
    }

    /**
     * set all object attributes from the array.
     *
     * @param array $data data array
     */
    protected function setAttributes(array $data)
    {
        if (isset($data[self::IPTC_CAPTION])) {
            $this->setCaption($data[self::IPTC_CAPTION][0]);
        }
        if (isset($data[self::IPTC_TITLE])) {
            $this->setTitle($data[self::IPTC_TITLE][0]);
        }
        if (isset($data[self::IPTC_COPYRIGHT])) {
            $this->setCopyright($data[self::IPTC_COPYRIGHT][0]);
        }
        if (isset($data[self::IPTC_AUTHOR])) {
            $this->setAuthor($data[self::IPTC_AUTHOR][0]);
        }
        if (isset($data[self::IPTC_KEYWORDS])) {
            $this->setKeywords($data[self::IPTC_KEYWORDS][0]);
        }
    }

    /**
     * check the encoding from data - if it is utf8 set the flag.
     *
     * @param array $data data array
     */
    private function setIsUtf8EncodedByData(array $data)
    {
        $this->isUtf8Encoded = false;

        if (isset($data[self::IPTC_CODE_CHARACTER_SET]) &&
            $data[self::IPTC_CODE_CHARACTER_SET][0] == "\x1b%G"
        ) {
            $this->isUtf8Encoded = true;
        }
    }

    /**
     * add one keyword.
     *
     * @param string $keyword keyword
     */
    private function addKeyword($keyword)
    {
        if ('' != $this->keywords) {
            $this->keywords .= ', ';
        }

        if ($this->isUtf8Encoded()) {
            $keyword = utf8_encode($keyword);
        }

        $this->keywords .= $keyword;
    }
}
