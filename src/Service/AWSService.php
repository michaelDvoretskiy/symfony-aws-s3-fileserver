<?php

namespace App\Service;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class AWSService
{

    const NO_SUCH_FILE_ERROR = 'NoSuchKey';

    private $client;

    public function __construct(
        private ContainerBagInterface $params,
        private JsonResponsesService $jsonRespService
    )
    {
        $this->client = $this->getClient();
    }

    public function getFile(string $key, string $bucket)
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $bucket, 
                'Key'    => $key,
            ]);    
        } catch(S3Exception $e) {
            if ($e->getAwsErrorCode() == 'NoSuchKey') {
                return [
                    'errCode' => self::NO_SUCH_FILE_ERROR
                ];
            }         
        } catch(Throwable $e) {
            $this->jsonRespService->generalError();
        }
        return $result;
    }

    public function putFile(string $key, string $bucket, string $path, $options = []) 
    {
        try {
            $objParams = [
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $path,
                // 'ACL'    => 'public-read',
                // 'Body' => $file_content,
            ];
            if ($options['contentType'] ?? false) {
                $objParams['ContentType'] = $options['contentType'] ;
            }
            $result = $this->client->putObject($objParams);
        } catch(Throwable $e) {
            $this->jsonRespService->generalError();
        }
        return $result;
    }

    public function removeFile(string $key, string $bucket) 
    {
        try {
            $objParams = [
                'Bucket' => $bucket,
                'Key'    => $key,
            ];
            $result = $this->client->deleteObject($objParams);
        } catch(Throwable $e) {
            $this->jsonRespService->generalError();
        }
        return $result;
    }

    public function removeMatchingFiles(string $path, string $bucket) 
    {
        try {
            if(substr($path, -1) != '/') {
                $path .= '/';
            }
            $result = $this->client->deleteMatchingObjects($bucket, $path);
        } catch(Throwable $e) {
            $this->jsonRespService->generalError();
        }
        return $result;
    }

    private function getClient()
    {
        $client = new S3Client([
            'region' => $this->params->get('aws.region'),
            'version' => $this->params->get('aws.version'),
            'credentials' => [
              'key' => $this->params->get('aws.key'),
              'secret' => $this->params->get('aws.secret')
            ],
            // Set the S3 class to use objects.dreamhost.com/bucket
            // instead of bucket.objects.dreamhost.com
            'use_path_style_endpoint' => true
          ]);
          
          return $client;
    }
}