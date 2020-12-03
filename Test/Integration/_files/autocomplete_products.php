<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$productsNames = include 'autocomplete_products_names.php';

$iterator = 1;

foreach($productsNames as $productName) {
    $product = $objectManager->create(\Magento\Catalog\Model\Product::class);
    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setId(555+$iterator)
        ->setAttributeSetId(4)
        ->setName($productName)
        ->setSku('autocomplete_'.$iterator)
        ->setPrice(10)
        ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->setWebsiteIds([1])
        ->setStoreIds([1])
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
        ->setCanSaveCustomOptions(true)
        ->setDescription('<p>Description</p>')
        ->save();

    $product->reindex();
    $product->priceReindexCallback();

    $iterator++;
}
