<?php

namespace MageSuite\Autocomplete\Api\Data;

interface SuggestedPhraseInterface
{
    /**
     * @return string
     */
    public function getPhrase();

    /**
     * @param string $phrase
     * @return self
     */
    public function setPhrase(string $phrase);

    /**
     * @return int
     */
    public function getProductsCount();

    /**
     * @param $productsCount
     * @return int
     */
    public function setProductsCount($productsCount);
}