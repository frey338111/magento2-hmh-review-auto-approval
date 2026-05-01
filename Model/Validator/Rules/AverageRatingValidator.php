<?php
declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator\Rules;

use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;

class AverageRatingValidator extends AbstractValidator
{
    private const MAX_RATING_VALUE = 5;

    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ReviewFactory $reviewFactory,
        private readonly VoteCollectionFactory $voteCollectionFactory
    ) {
    }

    public function isValid(Review $review): bool
    {
        $storeId = $this->getStoreId($review);
        $reviewRating = $this->getReviewRating((int) $review->getId());
        if ($reviewRating === null) {
            return false;
        }

        $productId = (int) $review->getEntityPkValue();
        $productAverageRating = $this->getProductAverageRating(
            $productId,
            $storeId
        );
        if ($productAverageRating === null) {
            return false;
        }

        $allowedDifference = $this->configProvider->getAverageRating($storeId);
        $minimumAllowedRating = $productAverageRating - $allowedDifference;

        return $reviewRating > $minimumAllowedRating;
    }

    private function getReviewRating(int $reviewId): ?float
    {
        if ($reviewId <= 0) {
            return null;
        }

        $votes = $this->voteCollectionFactory->create()
            ->setReviewFilter($reviewId);
        if ($votes->getSize() === 0) {
            return null;
        }

        $ratingTotal = 0;
        foreach ($votes as $vote) {
            $ratingTotal += (int) $vote->getValue();
        }

        return $ratingTotal / $votes->getSize();
    }

    private function getProductAverageRating(int $productId, ?int $storeId): ?float
    {
        if ($productId <= 0) {
            return null;
        }

        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException) {
            return null;
        }

        $ratingSummary = $this->getRatingSummary($product, $storeId);
        if ($ratingSummary !== null) {
            return ($ratingSummary / 100) * self::MAX_RATING_VALUE;
        }

        if ($storeId === null) {
            return null;
        }

        $ratingSummary = $this->getRatingSummary($product, null);
        return $ratingSummary !== null ? ($ratingSummary / 100) * self::MAX_RATING_VALUE : null;
    }

    private function getRatingSummary(ProductInterface $product, ?int $storeId): ?float
    {
        $this->reviewFactory->create()->getEntitySummary($product, $storeId ?? 0);
        $ratingSummary = $product->getRatingSummary();

        if (!$ratingSummary || (int) $ratingSummary->getReviewsCount() <= 0) {
            return null;
        }

        return (float) $ratingSummary->getRatingSummary();
    }
}
