<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Api;

use Magento\Review\Model\Review;

interface ReviewApprovalValidatorInterface
{
    public function isValid(Review $review): bool;
}
