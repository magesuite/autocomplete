<?php

namespace MageSuite\Autocomplete\Model\Autocomplete;

class SuggestedPhrasesProvider
{
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

    public function __construct(
        \Smile\ElasticsuiteCore\Search\Request\Builder $requestBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Smile\ElasticsuiteCore\Api\Client\ClientInterface $client,
        \Smile\ElasticsuiteCore\Search\Request\ContainerConfigurationFactory $containerConfigurationFactory,
        \Smile\ElasticsuiteCore\Api\Client\ClientConfigurationInterface $clientConfiguration,
        \Smile\ElasticsuiteCore\Client\ClientBuilder $clientBuilder
    )
    {
        $this->storeManager = $storeManager;
        $this->requestBuilder = $requestBuilder;
        $this->client = $client;
        $this->containerConfigurationFactory = $containerConfigurationFactory;
        $this->clientConfiguration = $clientConfiguration;
        $this->clientBuilder = $clientBuilder;

        $this->esClient = $clientBuilder->build($clientConfiguration->getOptions());
    }

    public function getSuggestions($phrase)
    {
        $params['index'] = $this->getIndexName();
        $params['body']['_source'] = 'entity_id';
        $params['body']['suggest'] = [
            'autocomplete_suggest' => [
                'prefix' => $phrase,
                "completion" => [
                    "field" => "autocomplete_suggest",
                    "skip_duplicates" => true,
                    "fuzzy" => [
                        "fuzziness" => 0
                    ]
                ]
            ]
        ];

        $queryResponse = $this->client->search($params);

        if(!isset($queryResponse["suggest"]["autocomplete_suggest"][0]["options"])) {
            return [];
        }

        $suggestions = [];

        foreach($queryResponse["suggest"]["autocomplete_suggest"][0]["options"] as $suggestion) {
            $suggestions[] = $suggestion['text'];
        }

        $start = microtime(true);

        $counts = $this->getSuggestionsProductsCounts($suggestions);

        echo ($start-microtime(true)).PHP_EOL;

        return $suggestions;
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

    protected function getSuggestionsProductsCounts(array $suggestions)
    {
        $params['body'] = [];

        foreach($suggestions as $suggestion) {
            $params['body'][] = [
                'index'=> $this->getIndexName()
            ];

            $params['body'][] = [
                'query' => [
                    'match' => [
                        'autocomplete_suggest' => [
                            'query' => $suggestion
                        ]
                    ]
                ],
            ];
        }

        $counts = $this->esClient->msearch($params);

        $resultIterator = 0;

        $results = [];

        foreach($suggestions as $suggestion) {
            if(isset($counts['responses'][$resultIterator]['hits']['total'])) {
                $results[$suggestion] = $counts['responses'][$resultIterator]['hits']['total'];
            }

            $resultIterator++;
        }

        return $results;
    }
}