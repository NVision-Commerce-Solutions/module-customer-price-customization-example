<?php

declare(strict_types=1);

namespace MyCustom\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\PriceDataBuilder;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CustomizePriceData
{
    private Product $resourceModel;
    private StoreManagerInterface $storeManager;
    private Config $config;

    public function __construct(Product $resourceModel, StoreManagerInterface $storeManager, Config $config)
    {
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @param PriceDataBuilder $subject
     * @param array $result
     * @param array $responseItem
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterBuild(PriceDataBuilder $subject, array $result, array $responseItem): array
    {
        $storeId = $this->storeManager->getStore()->getId();

        $acadonLength = $this->getAttributeValue($responseItem['productId'], 'acadon_length', $storeId);
        $acadonDiameter = $this->getAttributeValue($responseItem['productId'], 'acadon_diameter', $storeId);
        $acadonWidth = $this->getAttributeValue($responseItem['productId'], 'acadon_width', $storeId);
        $acadonThickness = $this->getAttributeValue($responseItem['productId'], 'acadon_thickness', $storeId);
        $result['price'] *= $acadonLength * $acadonDiameter * $acadonWidth * $acadonThickness;
        if ($this->config->useSpecialPrice()) {
            $result['special_price'] *= $acadonLength * $acadonDiameter * $acadonWidth * $acadonThickness;
        }

        return $result;
    }

    /**
     * @param $productId
     * @param $attributeName
     * @param $storeId
     * @return float
     */
    private function getAttributeValue($productId, $attributeName, $storeId): float
    {
        return (float) $this->resourceModel->getAttributeRawValue($productId, $attributeName, $storeId) ?: 1.0;
    }
}
