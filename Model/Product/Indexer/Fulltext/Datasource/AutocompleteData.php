<?php

namespace MageSuite\Autocomplete\Model\Product\Indexer\Fulltext\Datasource;

class AutocompleteData extends \Smile\ElasticsuiteCatalog\Model\Eav\Indexer\Fulltext\Datasource\AbstractAttributeData implements
    \Smile\ElasticsuiteCore\Api\Index\DatasourceInterface,
    \Smile\ElasticsuiteCore\Api\Index\Mapping\DynamicFieldProviderInterface
{
    public function __construct(
        \Smile\ElasticsuiteCatalog\Model\ResourceModel\Eav\Indexer\Fulltext\Datasource\AbstractAttributeData $resourceModel,
        \Smile\ElasticsuiteCore\Index\Mapping\FieldFactory $fieldFactory,
        \Smile\ElasticsuiteCatalog\Helper\AbstractAttribute $attributeHelper,
        array $indexedBackendModels = []
    ) {
        parent::__construct($resourceModel, $fieldFactory, $attributeHelper, $indexedBackendModels);

        $this->fields['autocomplete_suggest'] = $this->fieldFactory->create(
            [
                'name' => 'autocomplete_suggest',
                'type' => 'completion'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addData($storeId, array $indexData)
    {
        foreach ($indexData as $productId => $productData) {
            if (!isset($productData['name'][0])) {
                continue;
            }

            $name = $productData['name'][0];
            $name = str_replace(['-', '/', '"', '\'', '_', '+', ';', ',', '.'], ' ', $name);

            $words = explode(' ', $name);

            $indexData[$productId]['autocomplete_suggest'] = $words;
        }

        return $indexData;
    }
}
