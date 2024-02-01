<?php

namespace ADP\BaseVersion\Includes\Context\Container;

use ADP\BaseVersion\Includes\WC\WcCartItemFacade;

class ContainerCompatibilityManager
{
    /** @var array<int, ContainerCompatibility> */
    protected $registeredContainerCompatibilities;

    public function __construct()
    {
        $this->registeredContainerCompatibilities = [];
    }

    public function register(ContainerCompatibility $compatibility)
    {
        $this->registeredContainerCompatibilities[] = $compatibility;
    }

    public function isContainerProduct(\WC_Product $wcProduct): bool
    {
        foreach ($this->registeredContainerCompatibilities as $compatibility) {
            if ($compatibility->isActive() && $compatibility->isContainerProduct($wcProduct)) {
                return true;
            }
        }

        return false;
    }

    public function isContainerFacade(WcCartItemFacade $facade): bool
    {
        return $this->getCompatibilityFromContainerFacade($facade) !== null;
    }

    public function isPartOfContainer(WcCartItemFacade $facade): bool
    {
        return $this->getCompatibilityFromPartOfContainerFacade($facade) !== null;
    }

    public function getCompatibilityFromContainerFacade(WcCartItemFacade $facade): ?ContainerCompatibility
    {
        foreach ($this->registeredContainerCompatibilities as $compatibility) {
            if ($compatibility->isActive() && $compatibility->isContainerFacade($facade)) {
                return $compatibility;
            }
        }

        return null;
    }

    public function getCompatibilityFromPartOfContainerFacade(WcCartItemFacade $facade): ?ContainerCompatibility
    {
        foreach ($this->registeredContainerCompatibilities as $compatibility) {
            if ($compatibility->isActive() && $compatibility->isFacadeAPartOfContainer($facade)) {
                return $compatibility;
            }
        }

        return null;
    }

    public function getCompatibilityFromContainerWcProduct(\WC_Product $wcProduct): ?ContainerCompatibility
    {
        foreach ($this->registeredContainerCompatibilities as $compatibility) {
            if ($compatibility->isActive() && $compatibility->isContainerProduct($wcProduct)) {
                return $compatibility;
            }
        }

        return null;
    }
}
