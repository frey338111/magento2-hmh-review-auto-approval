<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Hmh\ReviewAutoApproval\Model\Validator\ValidatorPool;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;

class ReviewApprovalProcessor
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ReviewFactory $reviewFactory,
        private readonly ValidatorPool $validatorPool
    ) {
    }

    public function process(int $reviewId): void
    {
        if ($reviewId <= 0) {
            return;
        }

        $review = $this->reviewFactory->create()->load($reviewId);
        if (!$review->getId() || (int) $review->getStatusId() !== Review::STATUS_PENDING) {
            return;
        }

        if ((int) $review->getEntityId() !== (int) $review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)) {
            return;
        }

        $storeId = $this->getStoreId($review);
        if (!$this->configProvider->isEnabled($storeId)) {
            return;
        }

        $rules = $this->configProvider->getRules($storeId);
        if (!$this->validatorPool->isValid($review, $rules)) {
            return;
        }

        $review->setStatusId(Review::STATUS_APPROVED);
        $review->save();
        $review->aggregate();
    }

    private function getStoreId(Review $review): ?int
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
