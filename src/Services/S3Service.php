<?php

namespace App\Services;

use Aws\S3\S3Client;

class S3Service
{
    private $client;
    private $bucket;
    private $prefix;

    public function __construct()
    {
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['S3_REGION'],
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);
        $this->bucket = $_ENV['S3_BUCKET'];
        $this->prefix = $_ENV['S3_PREFIX'];
    }

    public function listFiles()
    {
        $result = $this->client->listObjectsV2([
            'Bucket' => $this->bucket,
            'Prefix' => $this->prefix,
        ]);

        $files = [];
        foreach ($result['Contents'] ?? [] as $object) {
            $files[] = $object['Key'];
        }
        return $files;
    }

    public function readFile($key)
    {
        $result = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        return explode("\n", $result['Body']);
    }

    public function isAlreadyProcessed($filename)
    {
        return false; // Stub
    }

    public function markAsProcessed($filename)
    {
        // Stub
    }
}
