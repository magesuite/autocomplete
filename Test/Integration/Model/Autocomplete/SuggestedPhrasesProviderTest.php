<?php

namespace MageSuite\Autocomplete\Test\Integration\Model\Autocomplete;

class SuggestedPhrasesProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MageSuite\ElasticSuiteAddons\Model\Autocomplete\SuggestedPhrasesProvider
     */
    protected $suggestedPhrasesProvider;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->suggestedPhrasesProvider = $this->objectManager->create(\MageSuite\ElasticSuiteAddons\Model\Autocomplete\SuggestedPhrasesProvider::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadAutocompleteProducts
     */
    public function testItReturnsCorrectAutocompletionPhrases()
    {
        $this->assertEquals(
            ['Joga', 'Jogging'],
            $this->suggestedPhrasesProvider->getSuggestions('jo')
        );
    }

    public static function loadAutocompleteProducts() {
        include __DIR__.'/../../_files/autocomplete_products.php';
    }
}