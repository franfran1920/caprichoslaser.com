<?php

namespace ADP\BaseVersion\Includes\CartProcessor\ToPricingCartItemAdapter;

use ADP\BaseVersion\Includes\CartProcessor\OriginalPriceCalculation;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Core\Cart\Cart;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Base\CartItemAttributeEnum;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Basic\BasicCartItem;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\ICartItem;
use ADP\BaseVersion\Includes\WC\WcCartItemFacade;
use ADP\Factory;

class SimpleToPricingCartItemAdapter implements IToPricingCartItemAdapter
{
    /** @var Context */
    protected $context;

    public function __construct()
    {
        $this->context = adp_context();
    }

    public function canAdaptFacade(WcCartItemFacade $facade): bool
    {
        return true;
    }

    public function adapt(WcCartItemFacade $facade, int $pos = -1): ?BasicCartItem
    {
        try {
            $origPriceCalc = new OriginalPriceCalculation($this->context);
            $origPriceCalc->withContext($this->context);
        } catch (\Exception $e) {
            return null;
        }

        Factory::callStaticMethod(
            'PriceDisplay_PriceDisplay',
            'processWithout',
            array($origPriceCalc, 'process'),
            $facade
        );

        $qty = floatval(apply_filters('wdp_get_product_qty', $facade->getQty(), $facade));

        /** Build generic item */
        $initialCost = $origPriceCalc->priceToAdjust;
        if ($facade->isImmutable() && $facade->getHistory()) {
            foreach ($facade->getHistory() as $amounts) {
                $initialCost += array_sum($amounts);
            }
        }
        $item = new BasicCartItem($facade, $initialCost, $qty, $pos);
        $item->prices()->setTrdPartyAdjustmentsTotal($origPriceCalc->trdPartyAdjustmentsAmount);
        /** Build generic item end */

        if ($origPriceCalc->isReadOnlyPrice) {
            $item->addAttr(CartItemAttributeEnum::READONLY_PRICE());
        }

        if ($facade->isImmutable()) {
            foreach ($facade->getPriceAdjustments() as $priceAdjustment) {
                $item->applyPriceAdjustment($priceAdjustment);
            }
            $item->addAttr(CartItemAttributeEnum::IMMUTABLE());
        }

        if (!$facade->isVisible()) {
            $item->addAttr(CartItemAttributeEnum::IMMUTABLE());
        }

        (new ToPricingAddonsAdapter())->adaptAddonsFromFacadeAndPutIntoPricingCartItem(
            $origPriceCalc,
            $facade,
            $item
        );

        return $item;
    }

    public function adaptFacadeAndPutIntoCart($cart, WcCartItemFacade $facade, int $pos): bool
    {
        /** @var Cart $cart */

        $item = $this->adapt($facade, $pos);

        if (!$item) {
            return false;
        }

        $cart->addToCart($item);

        return true;
    }

    public function canAdaptWcProduct(\WC_Product $product): bool
    {
        return true;
    }

    public function adaptWcProduct(\WC_Product $product, $cartItemData = []): ?ICartItem
    {
        $pos = -1;

        $facade = WcCartItemFacade::createFromProduct($this->context, $product, $cartItemData);
        $facade->withContext($this->context);

        try {
            $origPriceCalc = new OriginalPriceCalculation($this->context);
            $origPriceCalc->withContext($this->context);
        } catch (\Exception $e) {
            return null;
        }

        Factory::callStaticMethod(
            'PriceDisplay_PriceDisplay',
            'processWithout',
            array($origPriceCalc, 'process'),
            $facade
        );

        $qty = floatval(apply_filters('wdp_get_product_qty', $facade->getQty(), $facade));

        /** Build generic item */
        $initialCost = $origPriceCalc->priceToAdjust;
        if ($facade->isImmutable() && $facade->getHistory()) {
            foreach ($facade->getHistory() as $amounts) {
                $initialCost += array_sum($amounts);
            }
        }
        $item = new BasicCartItem($facade, $initialCost, $qty, $pos);
        $item->prices()->setTrdPartyAdjustmentsTotal($origPriceCalc->trdPartyAdjustmentsAmount);
        /** Build generic item end */

        (new ToPricingAddonsAdapter())->adaptAddonsFromFacadeAndPutIntoPricingCartItem(
            $origPriceCalc,
            $facade,
            $item
        );

        if ($origPriceCalc->isReadOnlyPrice) {
            $item->addAttr(CartItemAttributeEnum::READONLY_PRICE());
        }

        if ($facade->isImmutable()) {
            foreach ($facade->getPriceAdjustments() as $priceAdjustment) {
                $item->applyPriceAdjustment($priceAdjustment);
            }
            $item->addAttr(CartItemAttributeEnum::IMMUTABLE());
        }

        if (!$facade->isVisible()) {
            $item->addAttr(CartItemAttributeEnum::IMMUTABLE());
        }

        return $item;
    }
}
