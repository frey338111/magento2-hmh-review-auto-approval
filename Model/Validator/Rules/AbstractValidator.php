<?php
declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Rules;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Magento\Review\Model\Review;

abstract class AbstractValidator implements ReviewApprovalValidatorInterface
{
    protected function getStoreId(Review $review): ?int
    {
        $storeId = (int) $review->getStoreId();
        if ($storeId > 0) {
            return $storeId;
        }

        $stores = array_map('intval', (array) $review->getStores());
        foreach ($stores as $storeId) {
            if ($storeId > 0) {
                return $storeId;
            }
        }

        return null;
    }
}
