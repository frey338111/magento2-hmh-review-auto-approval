<?php
declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Hmh\ReviewAutoApproval\Model\Validator\Strategy\ValidationStrategyPool;
use Magento\Review\Model\Review;

class ValidatorPool
{
    /**
     * @param ReviewApprovalValidatorInterface[] $validators
     */
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ValidationStrategyPool $validationStrategyPool,
        private readonly array $validators = []
    ) {
    }

    public function isValid(
        Review $review,
        array $validatorNames = []
    ): bool {
        if ($validatorNames === []) {
            return false;
        }

        $validators = $this->getValidatorsByName($validatorNames);
        if ($validators === []) {
            return false;
        }

        return $this->validationStrategyPool
            ->get($this->configProvider->getApproveOn($this->getStoreId($review)))
            ->isValid($review, $validators);
    }

    public function getValidatorNames(): array
    {
        return array_keys($this->validators);
    }

    /**
     * @return ReviewApprovalValidatorInterface[]
     */
    private function getValidatorsByName(array $validatorNames): array
    {
        return array_intersect_key($this->validators, array_flip($validatorNames));
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
