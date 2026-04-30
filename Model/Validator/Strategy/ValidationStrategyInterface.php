<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Strategy;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Magento\Review\Model\Review;

interface ValidationStrategyInterface
{
    /**
     * @param ReviewApprovalValidatorInterface[] $validators
     */
    public function isValid(Review $review, array $validators): bool;
}
