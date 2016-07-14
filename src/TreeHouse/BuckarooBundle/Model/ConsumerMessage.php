<?php

declare (strict_types = 1);

namespace TreeHouse\BuckarooBundle\Model;

/**
 * Value object with status information that can be safely displayed to your customers.
 */
class ConsumerMessage
{
    /**
     * The locale in which the texts were written (e.g. nl_NL).
     *
     * @var string
     */
    private $culture;

    /**
     * A title text that can be used in feedback to the customer.
     *
     * @var string
     */
    private $title;

    /**
     * A body text that can be used in feedback to the customer, containing markup (HTML).
     *
     * @var string
     */
    private $htmlText;

    /**
     * A body text that can be used in feedback to the customer.
     *
     * @var string
     */
    private $plainText;

    /**
     * Whether it is important for the customer to read this message.
     *
     * @var bool
     */
    private $mustRead;

    /**
     * @param string $culture
     * @param string $title
     * @param string $htmlText
     * @param string $plainText
     * @param bool   $mustRead
     */
    public function __construct(
        string $culture,
        string $title,
        string $htmlText,
        string $plainText,
        bool $mustRead = true
    ) {
        $this->culture = $culture;
        $this->title = $title;
        $this->htmlText = $htmlText;
        $this->plainText = $plainText;
        $this->mustRead = $mustRead;
    }

    /**
     * @return string
     */
    public function getCulture() : string
    {
        return $this->culture;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getHtmlText() : string
    {
        return $this->htmlText;
    }

    /**
     * @return string
     */
    public function getPlainText() : string
    {
        return $this->plainText;
    }

    /**
     * @return bool
     */
    public function isMustRead() : bool
    {
        return $this->mustRead;
    }
}
