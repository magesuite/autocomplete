<?php

namespace MageSuite\Autocomplete\Model\Autocomplete;

class SuggestedPhrase implements \MageSuite\Autocomplete\Api\Data\SuggestedPhraseInterface
{
    /**
     * @param string $phrase
     * @param int $productsCount
     */
    public function __construct($phrase, $productsCount)
    {
        $this->phrase = $phrase;
        $this->productsCount = $productsCount;
    }

    /**
     * @var string
     */
    protected $phrase;

    /**
     * @var int
     */
    protected $productsCount = 0;

    /**
     * @inheritDoc
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * @inheritDoc
     */
    public function setPhrase(string $phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProductsCount()
    {
        return $this->productsCount;
    }

    /**
     * @inheritDoc
     */
    public function setProductsCount($productsCount)
    {
        $this->productsCount = $productsCount;

        return $this;
    }

    public function toString() {
        return sprintf('%s:%s', $this->getPhrase(), $this->getProductsCount());
    }
}