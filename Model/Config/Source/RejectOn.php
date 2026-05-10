<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Config\Source;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Framework\Option\ArrayInterface;

class RejectOn implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => ConfigProvider::APPROVE_ON_NO_RULE_PASSED,
                'label' => __('No Rules Passed'),
            ]
        ];
    }
}
