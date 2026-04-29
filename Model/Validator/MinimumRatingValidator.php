<?php
declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;

class MinimumRatingValidator implements ReviewApprovalValidatorInterface
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly VoteCollectionFactory $voteCollectionFactory
    ) {
    }

    public function isValid(Review $review): bool
    {
        $storeId = $this->getStoreId($review);
        $minimumRating = $this->configProvider->getMinimumRating($storeId);
        $votes = $this->voteCollectionFactory->create()
            ->setReviewFilter((int)$review->getId());
        if ($votes->getSize() === 0) {
            return false;
        }
        $ratingTotal = 0;
        foreach ($votes as $vote) {
            $ratingTotal += (int)$vote->getValue();
        }

        return ($ratingTotal / $votes->getSize()) >= $minimumRating;
    }

    private function getStoreId(Review $review): ?int
    {
        $storeId = (int)$review->getStoreId();
        if ($storeId > 0) {
            return $storeId;
        }
        $stores = array_map('intval', (array)$review->getStores());
        foreach ($stores as $storeId) {
            if ($storeId > 0) {
                return $storeId;
            }
        }

        return null;
    }
}
