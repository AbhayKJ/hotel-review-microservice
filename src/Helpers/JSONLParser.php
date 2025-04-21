<?php
namespace App\Helpers;

use Psr\Log\LoggerInterface;

class JSONLParser {
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function parseLine(string $line): ?array {
        $data = json_decode($line, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->warning("Invalid JSON: $line");
            return null;
        }

        if (!isset($data['hotelId'], $data['platform'], $data['comment']['rating'], $data['comment'], $data['overallByProviders'][0]['grades'])) {
            $this->logger->warning("Missing required fields: " . json_encode($data));
            return null;
        }

        return [
            'hotel_id' => $data['hotelId'],
            'platform' => $data['platform'],
            'hotel_name' => $data['hotelName'] ?? '',
            'rating' => $data['comment']['rating'],
            'review_text' => $data['comment']['reviewComments'] ?? '',
            'review_date' => $data['comment']['reviewDate'] ?? '',
            'country' => $data['comment']['reviewerInfo']['countryName'] ?? '',
            'language' => $data['comment']['translateSource'] ?? '',
            'provider_id' => $data['comment']['providerId'] ?? null,
            'extended_ratings' => $data['overallByProviders'][0]['grades'] ?? []
        ];
    }
}
