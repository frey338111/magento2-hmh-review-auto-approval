<?php
declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Rules;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;

class MinimumRatingValidator extends AbstractValidator
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
}
