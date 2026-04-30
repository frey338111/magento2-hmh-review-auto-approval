<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Strategy;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;

class ValidationStrategyPool
{
    /**
     * @param ValidationStrategyInterface[] $strategies
     */
    public function __construct(
        private readonly array $strategies = []
    ) {
    }

    public function get(string $approveOn): ValidationStrategyInterface
    {
        $strategy = $this->strategies[$approveOn]
            ?? $this->strategies[ConfigProvider::APPROVE_ON_ANY_RULE_PASSED]
            ?? null;

        if (!$strategy instanceof ValidationStrategyInterface) {
            throw new \InvalidArgumentException(sprintf('Validation strategy "%s" is not configured.', $approveOn));
        }

        return $strategy;
    }
}
