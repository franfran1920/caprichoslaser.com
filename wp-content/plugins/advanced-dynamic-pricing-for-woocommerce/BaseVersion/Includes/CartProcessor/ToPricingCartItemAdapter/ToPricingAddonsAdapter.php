<?php

namespace ADP\BaseVersion\Includes\CartProcessor\ToPricingCartItemAdapter;

use ADP\BaseVersion\Includes\CartProcessor\OriginalPriceCalculation;
use ADP\BaseVersion\Includes\Compatibility\FlexibleProductFieldsCmp;
use ADP\BaseVersion\Includes\Compatibility\PPOMCmp;
use ADP\BaseVersion\Includes\Compatibility\ThemehighExtraOptionsProCmp;
use ADP\BaseVersion\Includes\Compatibility\TmExtraOptionsCmp;
use ADP\BaseVersion\Includes\Compatibility\WcCustomProductAddonsCmp;
use ADP\BaseVersion\Includes\Compatibility\WcffCmp;
use ADP\BaseVersion\Includes\Compatibility\WcProductAddonsCmp;
use ADP\BaseVersion\Includes\Compatibility\YithAddonsCmp;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Base\CartItemAddonsCollection;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\ICartItem;
use ADP\BaseVersion\Includes\WC\WcCartItemFacade;

class ToPricingAddonsAdapter
{
    public function adaptAddonsFromFacadeAndPutIntoPricingCartItem(
        OriginalPriceCalculation $origPriceCalc,
        WcCartItemFacade $facade,
        ICartItem $cartItem
    ) {
        $addons = [];

        if (($tmCmp = new TmExtraOptionsCmp()) && $tmCmp->isActive()) {
            $addons = $tmCmp->getAddonsFromCartItem($facade);
        } elseif (($themeHighCmp = new ThemehighExtraOptionsProCmp()) && $themeHighCmp->isActive()) {
            $addons = $themeHighCmp->getAddonsFromCartItem($facade);
        } elseif (($wcProductAddonsCmp = new WcProductAddonsCmp()) && $wcProductAddonsCmp->isActive()) {
            $addons = $wcProductAddonsCmp->getAddonsFromCartItem($facade);
        } elseif (($wcCustomProductAddonsCmp = new WcCustomProductAddonsCmp()) && $wcCustomProductAddonsCmp->isActive()) {
            $addons = $wcCustomProductAddonsCmp->getAddonsFromCartItem($facade);
        } elseif (($yithAddonsCmp = new YithAddonsCmp()) && $yithAddonsCmp->isActive()) {
            $addons = $yithAddonsCmp->getAddonsFromCartItem($facade);
        } elseif (($flexibleProductFieldsCmp = new FlexibleProductFieldsCmp()) && $flexibleProductFieldsCmp->isActive()) {
            $addons = $flexibleProductFieldsCmp->getAddonsFromCartItem($facade);
        } elseif (($ppomCmp = new PPOMCmp()) && $ppomCmp->isActive()) {
            $addons = $ppomCmp->getAddonsFromCartItem($facade);
        } elseif (($wcffCmp = new WcffCmp()) && $wcffCmp->isActive()) {
            $addons = $wcffCmp->getAddonsFromCartItem($facade);
        } else {
            return;
        }

        if (count($addons) > 0) {
            $cartItem->setAddons($addons);

            $initialCost = $this->calculateInitialCost(
                $origPriceCalc,
                $facade,
                CartItemAddonsCollection::ofList($addons)
            );
            $cartItem->prices()->setOriginalPrice($initialCost);
        } else {
            $cartItem->prices()->setTrdPartyAdjustmentsTotal($origPriceCalc->trdPartyAdjustmentsAmount);
        }
    }

    protected function calculateInitialCost(
        OriginalPriceCalculation $origPriceCalc,
        WcCartItemFacade $facade,
        CartItemAddonsCollection $addonsCollection
    ) {
        $initialCost = $origPriceCalc->basePrice;

        $initialCost += array_sum(array_column($addonsCollection->toList(), 'price'));

        if ($facade->isImmutable() && $facade->getHistory()) {
            foreach ($facade->getHistory() as $amounts) {
                $initialCost += array_sum($amounts);
            }
        }

        return $initialCost;
    }
}
