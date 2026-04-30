<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Strategy;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Magento\Review\Model\Review;

class AnyRulePassedStrategy implements ValidationStrategyInterface
{
    public function isValid(Review $review, array $validators): bool
    {
        foreach ($validators as $validator) {
            if ($validator instanceof ReviewApprovalValidatorInterface && $validator->isValid($review)) {
                return true;
            }
        }

        return false;
    }
}
