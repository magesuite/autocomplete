<?php

namespace MageSuite\Autocomplete\Test\Integration\Model\Autocomplete;

class SuggestedPhrasesProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MageSuite\Autocomplete\Model\Autocomplete\SuggestedPhrasesProvider
     */
    protected $suggestedPhrasesProvider;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->suggestedPhrasesProvider = $this->objectManager->create(\MageSuite\Autocomplete\Model\Autocomplete\SuggestedPhrasesProvider::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_Autocomplete::Test/Integration/_files/autocomplete_products.php
     */
    public function testItReturnsCorrectAutocompletionPhrasesSortedByProductsCount()
    {
        $suggestedPhrases = $this->suggestedPhrasesProvider->getSuggestions('jo');

        $expectedPhrases = [
            'Jogging' => 3,
            'Joga' => 2
        ];

        foreach($expectedPhrases as $expectedPhrase => $expectedCount) {
            /** @var \MageSuite\Autocomplete\Api\Data\SuggestedPhraseInterface $suggestedPhrase */
            $suggestedPhrase = array_shift($suggestedPhrases);

            $this->assertEquals($expectedPhrase, $suggestedPhrase->getPhrase());
            $this->assertEquals($expectedCount, $suggestedPhrase->getProductsCount());
        }
    }
}
