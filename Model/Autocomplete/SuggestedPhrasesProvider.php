<?php

namespace MageSuite\Autocomplete\Model\Autocomplete;

class SuggestedPhrasesProvider
{
    const AUTOCOMPLETE_FIELD = 'autocomplete_suggest';

    protected $resultsCache = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Builder
     */
    protected $requestBuilder;

    /**
     * @var \Smile\ElasticsuiteCore\Api\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\ContainerConfigurationFactory
     */
    protected $containerConfigurationFactory;

    /**
     * @var \Smile\ElasticsuiteCore\Api\Client\ClientConfigurationInterface
     */
    protected $clientConfiguration;

    /**
     * @var \Smile\ElasticsuiteCore\Client\ClientBuilder
     */
    protected $clientBuilder;

    /**
     * @var \Elasticsearch\Client
     */
    protected $esClient;

    /**
     * @var \MageSuite\Autocomplete\Api\Data\SuggestedPhraseInterfaceFactory
     */
    protected $suggestedPhraseFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $visibility;

    public function __construct(
        \Smile\ElasticsuiteCore\Search\Request\Builder $requestBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Smile\ElasticsuiteCore\Api\Client\ClientInterface $client,
        \Smile\ElasticsuiteCore\Search\Request\ContainerConfigurationFactory $containerConfigurationFactory,
        \Smile\ElasticsuiteCore\Api\Client\ClientConfigurationInterface $clientConfiguration,
        \Smile\ElasticsuiteCore\Client\ClientBuilder $clientBuilder,
        \MageSuite\Autocomplete\Api\Data\SuggestedPhraseInterfaceFactory $suggestedPhraseFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility
    )
    {
        $this->storeManager = $storeManager;
        $this->requestBuilder = $requestBuilder;
        $this->client = $client;
        $this->containerConfigurationFactory = $containerConfigurationFactory;
        $this->clientConfiguration = $clientConfiguration;
        $this->clientBuilder = $clientBuilder;

        $this->esClient = $clientBuilder->build($clientConfiguration->getOptions());
        $this->suggestedPhraseFactory = $suggestedPhraseFactory;
        $this->visibility = $visibility;
    }

    /**
     * @param $prefix
     * @return \MageSuite\Autocomplete\Api\Data\SuggestedPhraseInterface[]
     */
    public function getSuggestions($prefix)
    {
        if(isset($this->resultsCache[$prefix])) {
            return $this->resultsCache[$prefix];
        }

        $phrases = $this->getPhrases($prefix);

        if(empty($phrases)) {
            $this->resultsCache[$prefix] = [];
            return [];
        }

        $counts = $this->getProductsCountForPhrases($phrases);

        arsort($counts);

        $suggestedPhrases = [];

        foreach ($counts as $phrase => $productsCount) {
            $suggestedPhrases[] = $this->suggestedPhraseFactory->create([
                'phrase' => $phrase,
                'productsCount' => $productsCount
            ]);
        }

        $this->resultsCache[$prefix] = $suggestedPhrases;

        return $suggestedPhrases;
    }

    public function getIndexName()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $containerName = 'catalog_view_container';

        $config = $this->containerConfigurationFactory->create(
            ['containerName' => $containerName, 'storeId' => $storeId]
        );

        return $config->getIndexName();
    }

    protected function getProductsCountForPhrases(array $suggestions)
    {
        $params['body'] = [];

        foreach ($suggestions as $suggestion) {
            $params['body'][] = [
                'index' => $this->getIndexName()
            ];

            $params['body'][] = [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'spelling' => [
                                    'query' => $suggestion
                                ],
                            ],
                        ],
                        'filter' => [
                            ['term' => ['stock.is_in_stock' => true]],
                            ['terms' => ['visibility' => $this->visibility->getVisibleInSearchIds()]]
                        ]
                    ],
                ],
            ];
        }

        $counts = $this->esClient->msearch($params);

        $resultIterator = 0;

        $results = [];

        foreach ($suggestions as $suggestion) {
            if (isset($counts['responses'][$resultIterator]['hits']['total'])) {
                $results[$suggestion] = $counts['responses'][$resultIterator]['hits']['total'];
            }

            $resultIterator++;
        }

        return $results;
    }

    protected function getPhrases($prefix) {
        $params['index'] = $this->getIndexName();
        $params['body']['_source'] = 'entity_id';
        $params['body']['suggest'] = [
            self::AUTOCOMPLETE_FIELD => [
                'prefix' => $prefix,
                "completion" => [
                    "field" => self::AUTOCOMPLETE_FIELD,
                    "skip_duplicates" => true,
                    "fuzzy" => [
                        "fuzziness" => 0
                    ]
                ]
            ]
        ];

        $queryResponse = $this->client->search($params);

        if (!isset($queryResponse["suggest"][self::AUTOCOMPLETE_FIELD][0]["options"])) {
            return [];
        }

        $phrases = [];

        foreach ($queryResponse["suggest"][self::AUTOCOMPLETE_FIELD][0]["options"] as $suggestion) {
            $phrases[] = $suggestion['text'];
        }

        return $phrases;
    }
}