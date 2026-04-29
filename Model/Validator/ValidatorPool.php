<?php
declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\Validator;

use Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface;
use Hmh\ReviewAutoApproval\Model\Config\ConfigProvider;
use Magento\Review\Model\Review;

class ValidatorPool
{
    /**
     * @param ReviewApprovalValidatorInterface[] $validators
     */
    public function __construct(
        private readonly array $validators = []
    ) {
    }

    public function isValid(
        Review $review,
        array $validatorNames = [],
        string $approveOn = ConfigProvider::APPROVE_ON_ALL_RULES_PASSED
    ): bool {
        if ($validatorNames === []) {
            return false;
        }
        $validators = array_intersect_key($this->validators, array_flip($validatorNames));
        if ($validators === []) {
            return false;
        }

        $isAllMode = $approveOn === ConfigProvider::APPROVE_ON_ALL_RULES_PASSED;
        foreach ($validators as $validator) {
            $isValid = $validator instanceof ReviewApprovalValidatorInterface && $validator->isValid($review);
            if ($isAllMode && !$isValid) {
                return false;
            }
            if (!$isAllMode && $isValid) {
                return true;
            }
        }

        return $isAllMode;
    }

    public function getValidatorNames(): array
    {
        return array_keys($this->validators);
    }
}
