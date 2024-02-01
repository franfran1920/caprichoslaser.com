<?php

namespace ADP\BaseVersion\Includes\PriceDisplay\WcProductProcessor;

use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Basic\BasicCartItem;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Container\ContainerCartItem;
use ADP\BaseVersion\Includes\PriceDisplay\ProcessedProductContainer;
use ADP\BaseVersion\Includes\PriceDisplay\ProcessedProductSimple;
use WC_Product;

class WcProductProcessorHelper
{
    public static function isCalculatingPartOfContainerProduct(WC_Product $wcProduct): bool
    {
        $context = adp_context();
        $bundleProduct = self::getBundleProductFromBundled($wcProduct);

        if ($bundleProduct === null) {
            return false;
        }

        $cmp = $context->getContainerCompatibilityManager()->getCompatibilityFromContainerWcProduct($bundleProduct);

        if ($cmp === null) {
            return false;
        }

        $containerItem = $cmp->adaptContainerWcProduct($bundleProduct);

        foreach ($containerItem->getItems() as $bundledItem) {
            if (intval($bundledItem->getWcItem()->getProduct()->get_id()) === intval($wcProduct->get_id())) {
                return true;
            }
        }

        return false;
    }

    public static function getBundleProductFromBundled(WC_Product $wcProduct): ?WC_Product
    {
        $context = adp_context();
        $bundleProduct = ($GLOBALS['product'] ?? null);

        if (
            $bundleProduct === null
            || is_string($bundleProduct)
            || !$context->getContainerCompatibilityManager()->isContainerProduct($bundleProduct)
        ) {
            return null;
        }

        return $bundleProduct;
    }

    public static function tmpItemsToProcessedProduct($context, $product, $tmpItems, $tmpFreeItems, $tmpListOfFreeCartItemChoices)
    {
        $allItemsAreBasic = null;
        $allItemsAreContainers = null;
        foreach ($tmpItems as $item) {
            if ($item instanceof ContainerCartItem) {
                $allItemsAreBasic = false;
                if ($allItemsAreContainers === null) {
                    $allItemsAreContainers = true;
                }
            } elseif ($item instanceof BasicCartItem) {
                $allItemsAreContainers = false;
                if ($allItemsAreBasic === null) {
                    $allItemsAreBasic = true;
                }
            } else {
                $allItemsAreBasic = false;
                $allItemsAreContainers = false;
            }
        }

        if ($allItemsAreBasic === true) {
            $processedProduct = new ProcessedProductSimple(
                $context,
                $product,
                $tmpItems,
                $tmpFreeItems,
                $tmpListOfFreeCartItemChoices
            );
        } elseif ($allItemsAreContainers === true) {
            $processedProduct = new ProcessedProductContainer(
                $context,
                $product,
                $tmpItems,
                $tmpFreeItems,
                $tmpListOfFreeCartItemChoices
            );
        } else {
            return null;
        }

        return $processedProduct;
    }
}
