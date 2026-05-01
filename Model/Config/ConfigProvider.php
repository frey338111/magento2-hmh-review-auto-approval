<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    public const APPROVE_ON_ALL_RULES_PASSED = 'all_rules_passed';
    public const APPROVE_ON_ANY_RULE_PASSED = 'any_rule_passed';

    public const XML_PATH_ENABLED = 'hmh_review_auto_approval/general/enable';
    public const XML_PATH_RULES = 'hmh_review_auto_approval/general/rules';
    public const XML_PATH_APPROVE_ON = 'hmh_review_auto_approval/general/approve_on';
    public const XML_PATH_MINIMUM_RATING = 'hmh_review_auto_approval/default/minimum_rating';
    public const XML_PATH_AVERAGE_RATING = 'hmh_review_auto_approval/default/average_rating';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getMinimumRating(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_MINIMUM_RATING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getAverageRating(?int $storeId = null): float
    {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_AVERAGE_RATING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getRules(?int $storeId = null): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_RULES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($value === '') {
            return [];
        }

        return explode(',', $value);
    }

    public function getApproveOn(?int $storeId = null): string
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_APPROVE_ON,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value !== '' ? $value : self::APPROVE_ON_ALL_RULES_PASSED;
    }
}
