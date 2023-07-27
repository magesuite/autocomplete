<?php

namespace MageSuite\Autocomplete\Plugin\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider;

class BoldSearchedQueryInResults
{
    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;

    /**
     * @var \MageSuite\Autocomplete\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Search\Model\QueryFactory $queryFactory,
        \MageSuite\Autocomplete\Helper\Configuration $configuration
    )
    {
        $this->queryFactory = $queryFactory;
        $this->configuration = $configuration;
    }

    public function afterGetItems(\Smile\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider $subject, $result) {
        if(!$this->configuration->isBoldingOfSearchQueryEnabled()) {
            return $result;
        }

        $query = $this->queryFactory->get();

        $result = $this->boldSearchedQuery($query->getQueryText(), $result);

        return $result;
    }

    protected function boldSearchedQuery($queryText, array $result)
    {
        if(empty($result)) {
            return $result;
        }

        foreach($result as $term) {
            if(strpos($term->getTitle(), '<strong>') !== false) {
                continue;
            }

            $regularExpression = sprintf('/%s/i', preg_quote($queryText, '/'));

            $boldedTitle = @preg_replace($regularExpression, '<strong>$0</strong>', $term->getTitle());

            if(empty($boldedTitle)) {
                continue;
            }

            $term->setTitle($boldedTitle);
        }

        return $result;
    }


}
