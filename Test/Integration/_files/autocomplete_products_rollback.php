<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$registry = $objectManager->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productsNames = include 'autocomplete_products_names.php';

$iterator = 1;

foreach($productsNames as $productName) {
    $productId = 555 + $iterator;

    $product = $objectManager->create(\Magento\Catalog\Model\Product::class);
    $product->load($productId);

    if ($product->getId()) {
        $product->delete();
    }

    $iterator++;
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
