<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Plugin;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Review\Model\Review;
use Psr\Log\LoggerInterface;

class ReviewAggregatePlugin
{
    private const TOPIC_REVIEW_AUTO_APPROVAL = 'hmh.review.auto.approval';

    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly PublisherInterface $publisher,
        private readonly LoggerInterface $logger
    ) {
    }

    public function afterAggregate(Review $subject, Review $result): Review
    {
        if (!$subject->getId() || (int) $subject->getStatusId() !== Review::STATUS_PENDING) {
            return $result;
        }

        if ((int) $subject->getEntityId() !== (int) $subject->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)) {
            return $result;
        }

        $storeId = $this->getStoreId($subject);
        if (!$this->configProvider->isEnabled($storeId)) {
            return $result;
        }

        try {
            $this->publisher->publish(
                self::TOPIC_REVIEW_AUTO_APPROVAL,
                json_encode(['review_id' => (int) $subject->getId()], JSON_THROW_ON_ERROR)
            );
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Failed to publish review auto approval message.',
                [
                    'review_id' => (int) $subject->getId(),
                    'exception' => $exception->getMessage(),
                ]
            );
        }

        return $result;
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
