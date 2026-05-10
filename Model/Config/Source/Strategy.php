<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Strategy implements ArrayInterface
{
    public function __construct(
        private readonly array $options = []
    ) {
    }

    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->options as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => __((string) $label),
            ];
        }

        return $options;
    }
}
