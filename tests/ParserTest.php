<?php

use PHPUnit\Framework\TestCase;
use App\Helpers\JSONLParser;

class ParserTest extends TestCase
{
    protected JSONLParser $parser;

    protected function setUp(): void
    {
        $this->parser = new JSONLParser();
    }

    public function testValidJsonLineReturnsParsedArray(): void
    {
        $jsonLine = json_encode([
            "hotelId" => 10984,
            "platform" => "Agoda",
            "hotelName" => "Oscar Saigon Hotel",
            "comment" => [
                "rating" => 6.4,
                "reviewComments" => "Nice place.",
                "reviewDate" => "2025-04-10T05:37:00+07:00"
            ]
        ]);

        $result = $this->parser->parseLine($jsonLine);

        $this->assertIsArray($result);
        $this->assertEquals(10984, $result['hotel_id']);
        $this->assertEquals('Agoda', $result['platform']);
        $this->assertEquals('Oscar Saigon Hotel', $result['hotel_name']);
        $this->assertEquals(6.4, $result['rating']);
        $this->assertEquals('Nice place.', $result['review_text']);
    }

    public function testMissingRequiredFieldsReturnsNull(): void
    {
        $jsonLine = json_encode([
            "hotelId" => 10984,
            // Missing "comment"
        ]);

        $result = $this->parser->parseLine($jsonLine);

        $this->assertNull($result);
    }

    public function testMalformedJsonReturnsNull(): void
    {
        $jsonLine = '{"invalidJson":}';

        $result = $this->parser->parseLine($jsonLine);

        $this->assertNull($result);
    }
}
