<?php
namespace App\Repositories;

use App\Models\Review;
use Psr\Log\LoggerInterface;

class ReviewRepository {
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function save(array $review): bool {
        try {
            Review::updateOrCreate(
                ['hotel_id' => $review['hotel_id'], 'review_date' => $review['review_date']],
                $review
            );
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Failed to save review: " . $e->getMessage());
            return false;
        }
    }
}
