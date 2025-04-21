<?php

namespace App\Helpers;

class JSONLParser
{
    public function parseLine($line)
    {
        $data = json_decode($line, true);

        if (!$data || !isset($data['hotelId']) || !isset($data['comment']['reviewComments'])) {
            return null;
        }

        return [
            'hotel_id' => $data['hotelId'],
            'platform' => $data['platform'] ?? null,
            'hotel_name' => $data['hotelName'] ?? null,
            'review_text' => $data['comment']['reviewComments'],
            'rating' => $data['comment']['rating'] ?? null,
            'review_date' => $data['comment']['reviewDate'] ?? null,
        ];
    }
}
