<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Strategy;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Magento\Review\Model\Review;

class NoRulePassedStrategy implements ValidationStrategyInterface
{
    public function isValid(Review $review, array $validators): bool
    {
        foreach ($validators as $validator) {
            if (!$validator instanceof ReviewApprovalValidatorInterface) {
                continue;
            }

            if ($validator->isValid($review)) {
                return false;
            }
        }

        return true;
    }
}
