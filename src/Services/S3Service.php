<?php
namespace App\Services;

use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;

class S3Service {
    private S3Client $s3;
    private string $bucket;
    private string $prefix;
    private LoggerInterface $logger;

    public function __construct(S3Client $s3, string $bucket, string $prefix, LoggerInterface $logger) {
        $this->s3 = $s3;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
        $this->logger = $logger;
    }

    public function listNewFiles(): array {
        $objects = $this->s3->getPaginator('ListObjectsV2', [
            'Bucket' => $this->bucket,
            'Prefix' => $this->prefix,
        ]);

        $files = [];
        foreach ($objects as $result) {
            foreach ($result['Contents'] as $object) {
                $filename = $object['Key'];
                if (!str_ends_with($filename, '.processed')) {
                    $files[] = $filename;
                }
            }
        }
        return $files;
    }

    public function readFile(string $filename): array {
        try {
            $result = $this->s3->getObject(['Bucket' => $this->bucket, 'Key' => $filename]);
            return explode(PHP_EOL, (string) $result['Body']);
        } catch (\Exception $e) {
            $this->logger->error("Failed to read file: $filename - " . $e->getMessage());
            return [];
        }
    }

    public function markFileAsProcessed(string $filename): void {
        try {
            $this->s3->copyObject([
                'Bucket' => $this->bucket,
                'CopySource' => "{$this->bucket}/$filename",
                'Key' => "$filename.processed"
            ]);
            $this->s3->deleteObject(['Bucket' => $this->bucket, 'Key' => $filename]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to mark file as processed: $filename - " . $e->getMessage());
        }
    }
}
