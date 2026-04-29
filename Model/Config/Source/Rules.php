<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Config\Source;

use Hmh\ReviewAutoApproval\Model\Validator\ValidatorPool;
use Magento\Framework\Option\ArrayInterface;

class Rules implements ArrayInterface
{
    public function __construct(
        private readonly ValidatorPool $validatorPool
    ) {
    }

    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->validatorPool->getValidatorNames() as $validatorName) {
            $options[] = [
                'value' => $validatorName,
                'label' => __(ucwords(str_replace('_', ' ', (string) $validatorName))),
            ];
        }

        return $options;
    }
}
