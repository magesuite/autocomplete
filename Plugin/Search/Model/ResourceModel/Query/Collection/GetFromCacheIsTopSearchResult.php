<?php

declare(strict_types=1);

namespace MageSuite\Autocomplete\Plugin\Search\Model\ResourceModel\Query\Collection;

class GetFromCacheIsTopSearchResult
{
    protected const CACHE_KEY = 'is_top_search_result_%s';

    protected \Magento\Framework\App\CacheInterface $cacheInterface;
    protected \MageSuite\Autocomplete\Helper\Configuration $configuration;
    protected \Magento\Framework\DB\Adapter\AdapterInterface $connection;
    protected \Psr\Log\LoggerInterface $logger;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Framework\App\CacheInterface $cacheInterface,
        \MageSuite\Autocomplete\Helper\Configuration $configuration,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->cacheInterface = $cacheInterface;
        $this->configuration = $configuration;
        $this->connection = $resourceConnection->getConnection();
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    public function aroundIsTopSearchResult(
        \Magento\Search\Model\ResourceModel\Query\Collection $subject,
        callable $proceed,
        string $term,
        int $storeId,
        int $maxCountCacheableSearchTerms
    ) {
        if (!$this->configuration->isEnabledTopSearchResultCache()) {
            return $proceed($term, $storeId, $maxCountCacheableSearchTerms);
        }

        try {
            $identifier = sprintf(self::CACHE_KEY, $storeId);

            if (!$topSearchResultJson = $this->cacheInterface->load($identifier)) {
                $topSearchResult = $this->getTopSearchResult($subject, $storeId, $maxCountCacheableSearchTerms);
                $topSearchResultJson = $this->serializer->serialize($topSearchResult);
                $this->cacheInterface->save($topSearchResultJson, $identifier, [], $this->configuration->getTopSearchResultCacheTTL());
            } else {
                $topSearchResult = $this->serializer->unserialize($topSearchResultJson);
            }
        } catch (\Exception $e) {
            $this->logger->debug(__('Error during getting top search result from cache: %1', $e->getMessage()));
        }

        if (!is_array($topSearchResult)) {
            return false;
        }

        return in_array($term, $topSearchResult);
    }

    protected function getTopSearchResult(
        \Magento\Search\Model\ResourceModel\Query\Collection $subject,
        int $storeId,
        int $maxCountCacheableSearchTerms
    ): array {
        $select = $subject->getSelect();
        $select->reset(\Magento\Framework\DB\Select::FROM);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->distinct(true);
        $select->from(['main_table' => $this->connection->getTableName('search_query')], ['query_text']);
        $select->where('main_table.store_id IN (?)', $storeId);
        $select->where('num_results > 0');
        $select->order(['popularity desc']);

        $select->limit($maxCountCacheableSearchTerms);

        return $this->connection->fetchCol($select);
    }
}
