<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Config\Source;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Framework\Option\ArrayInterface;

class ApproveOn implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => ConfigProvider::APPROVE_ON_ALL_RULES_PASSED,
                'label' => __('All Rules Passed'),
            ],
            [
                'value' => ConfigProvider::APPROVE_ON_ANY_RULE_PASSED,
                'label' => __('Any Rule Passed'),
            ],
        ];
    }
}
