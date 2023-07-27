<?php

namespace MageSuite\Autocomplete\Test\Unit\Plugin\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider;

class BoldSearchedQueryInResultsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $configuration;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $query;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $queryFactory;

    /**
     * @var \MageSuite\Autocomplete\Plugin\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider\BoldSearchedQueryInResults
     */
    protected $plugin;

    /**
     * @var \Magento\Search\Model\Autocomplete\ItemFactory
     */
    protected $termFactory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $dataProvider;

    protected function setUp(): void
    {
        $this->configuration = $this->getMockBuilder(\MageSuite\Autocomplete\Helper\Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configuration->method('isBoldingOfSearchQueryEnabled')->willReturn(true);

        $this->query = $this->getMockBuilder(\Magento\Search\Model\Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryFactory = $this->getMockBuilder(\Magento\Search\Model\QueryFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryFactory->method('get')->willReturn($this->query);

        $this->plugin = new \MageSuite\Autocomplete\Plugin\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider\BoldSearchedQueryInResults(
            $this->queryFactory,
            $this->configuration
        );

        $this->termFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Search\Model\Autocomplete\ItemFactory::class);

        $this->dataProvider = $this->getMockBuilder(\Smile\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testResultsAreBolded()
    {
        $this->query->method('getQueryText')->willReturn('je');

        $expectedTerms = [
            'jeans' => '<strong>je</strong>ans',
            'Jeanny' => '<strong>Je</strong>anny'
        ];

        foreach ($expectedTerms as $originalTerm => $boldedTerm) {
            $terms = [
                $this->termFactory->create([
                    'title' => $originalTerm,
                    'num_results' => 100,
                    'type' => 'term'
                ])
            ];

            $result = $this->plugin->afterGetItems($this->dataProvider, $terms);

            $this->assertEquals($boldedTerm, $result[0]->getTitle());
        }
    }
}
