<?php

require 'vendor/autoload.php';
require 'src/bootstrap.php';

use App\Services\S3Service;
use App\Helpers\JSONLParser;
use App\Repositories\ReviewRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Set up logger
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('logs/app.log', Logger::DEBUG));

// Load config
$bucket = getenv('S3_BUCKET');
$prefix = getenv('S3_PREFIX');

// Set up services
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region' => getenv('AWS_REGION'),
    'credentials' => [
        'key' => getenv('AWS_ACCESS_KEY_ID'),
        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    ],
]);

$s3Service = new S3Service($s3, $bucket, $prefix, $logger);
$parser = new JSONLParser($logger);
$repository = new ReviewRepository($logger);

// Process files
$files = $s3Service->listNewFiles();
foreach ($files as $file) {
    $lines = $s3Service->readFile($file);
    foreach ($lines as $line) {
        $data = $parser->parseLine($line);
        if ($data) {
            $repository->save($data);
        }
    }
    $s3Service->markFileAsProcessed($file);
}
