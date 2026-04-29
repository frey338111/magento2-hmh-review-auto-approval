<?php

declare(strict_types=1);

namespace Hmh\ReviewAutoApproval\Model\MessageQueue;

use Hmh\ReviewAutoApproval\Model\ReviewApprovalProcessor;
use Psr\Log\LoggerInterface;

class ReviewApprovalConsumer
{
    public function __construct(
        private readonly ReviewApprovalProcessor $reviewApprovalProcessor,
        private readonly LoggerInterface $logger
    ) {
    }

    public function process(string $message): void
    {
        $reviewId = $this->extractReviewId($message);

        if ($reviewId <= 0) {
            $this->logger->error(
                'Review auto approval message is missing a valid review ID.',
                ['message' => $message]
            );
            return;
        }

        try {
            $this->reviewApprovalProcessor->process($reviewId);
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Failed to process review auto approval message.',
                [
                    'review_id' => $reviewId,
                    'exception' => $exception->getMessage(),
                ]
            );
        }
    }

    private function extractReviewId(string $message): int
    {
        if (ctype_digit($message)) {
            return (int) $message;
        }

        $payload = json_decode($message, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return 0;
        }

        return (int) ($payload['review_id'] ?? 0);
    }
}
