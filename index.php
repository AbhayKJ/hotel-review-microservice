<?php

require 'vendor/autoload.php';
require 'src/bootstrap.php';

use App\Services\S3Service;
use App\Helpers\JSONLParser;
use App\Models\Review;

$command = $argv[1] ?? null;

if ($command === 'ingest') {
    echo "Starting ingestion...\n";
    $s3 = new S3Service();
    $parser = new JSONLParser();

    $files = $s3->listFiles();

    foreach ($files as $file) {
        if ($s3->isAlreadyProcessed($file)) {
            echo "Skipping already processed: $file\n";
            continue;
        }

        $lines = $s3->readFile($file);
        foreach ($lines as $line) {
            $review = $parser->parseLine($line);
            if ($review) {
                Review::create($review);
            }
        }

        $s3->markAsProcessed($file);
    }

    echo "Ingestion complete.\n";
} else {
    echo "Usage: php index.php ingest\n";
}
