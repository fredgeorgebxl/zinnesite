<?php

namespace IrishDan\ResponsiveImageBundle;

use Aws\CommandPool;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

/**
 * Class S3Bridge
 *
 * @package ResponsiveImageBundle
 */
class S3Bridge
{
    /**
     * @var
     */
    private $bucket;
    /**
     * @var
     */
    private $region;
    /**
     * @var string
     */
    private $directory;
    /**
     * @var
     */
    private $key;
    /**
     * @var
     * ['system/location', 'styled/location']
     */
    private $paths;
    /**
     * @var
     */
    private $secret;
    /**
     * @var
     */
    private $s3;

    /**
     * S3Bridge constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        if (!empty($config['bucket'])) {
            $this->bucket = $config['bucket'];
            $this->directory = empty($config['directory']) ? '' : $config['directory'] . '/';
            $this->key = $config['access_key_id'];
            $this->region = $config['region'];
            $this->secret = $config['secret_access_key'];
            $this->version = $config['version'];
        }
    }

    /**
     * @param $storePath
     * @param $key
     */
    public function fetchS3Object($storePath, $key)
    {
        $this->getClient();
        // Save object to a file.
        $result = $this->s3->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SaveAs' => $storePath,
        ]);
    }

    /**
     * Initialise the S3 client.
     */
    public function getClient()
    {
        // AWS access info
        $this->s3 = S3Client::factory([
            'version' => $this->version,
            'region' => $this->region,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
            ],
        ]);
    }

    /**
     * Removes all the images set in the $this->paths array from the configured S3 bucket.
     */
    public function removeFromS3()
    {
        $this->getClient();
        $objects = [];
        foreach ($this->paths as $path => $file) {
            $objects[] = ['Key' => $this->directory . $file];
        }
        $result = $this->s3->deleteObjects([
            'Bucket' => $this->bucket,
            'Delete' => [
                'Objects' => $objects,
            ],
        ]);
    }

    /**
     * @param      $paths
     * @param bool $clear
     */
    public function setPaths($paths, $clear = false)
    {
        if ($clear) {
            $this->paths = [];
        }
        foreach ($paths as $systemLocation => $styledLocation) {
            $this->paths[$systemLocation] = $styledLocation;
        }
    }

    /**
     * Tranfers all the images set in the $this->paths array to the configured S3 bucket.
     */
    public function uploadToS3()
    {
        $this->getClient();
        $commands = [];
        foreach ($this->paths as $path => $file) {
            $commands[] = $this->s3->getCommand(
                'PutObject', [
                    'region' => $this->region,
                    'Bucket' => $this->bucket,
                    'Key' => $this->directory . $file,
                    'SourceFile' => $path,
                    'ACL' => 'public-read',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                ]
            );

            $pool = new CommandPool($this->s3, $commands);
            $promise = $pool->promise();

            // Force the pool to complete synchronously
            try {
                $result = $promise->wait();
            } catch (AwsException $e) {
                // @TODO: Handle the error.
            }
        }
    }

    /**
     * @return array
     */
    public function listImages()
    {
        $images = [];
        $this->getClient();

        try {
            $iterator = $this->s3->getIterator('ListObjects', [
                'Bucket' => $this->bucket,
            ]);

            foreach ($iterator as $object) {
                $images[] = $object;
            }
        } catch (AwsException $exception) {
            // @TODO: Improve exceptions handling.
        }

        return $images;
    }
}