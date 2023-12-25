<?php

namespace App\Service;

use App\Service\AWSService;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class AWSImageService
{
    private $imagePathPrefix = "images/";
    private $thumbnailPathPrefix = "thumbnail/";
    private $thumbnailLocalPath = "/public/media/cache/thumbnail/";

    public function __construct(
        private AWSService $awsService, 
        private ContainerBagInterface $params,
        private FilterService $filterService,
        private JsonResponsesService $jsonRespService
    ) {}

    public function getImage(string $key)
    {
        return $this->awsService->getFile(
            $this->imagePathPrefix . $key, $this->params->get('aws.image_bucket')
        );
    }

    public function getThumbnail(string $key)
    {
        $res = $this->awsService->getFile(
            $this->thumbnailPathPrefix . $key, $this->params->get('aws.image_bucket')
        );
        if ($res['errCode'] ?? '' == AWSService::NO_SUCH_FILE_ERROR) {
            return $this->createThumbnail($key);      
        }
        return $res;
    }

    private function createThumbnail(string $key) 
    {
        try {
            $localUrl = $this->filterService->getUrlOfFilteredImage($key, 'thumbnail');
        } catch (NotLoadableException $e) {
            $this->jsonRespService->fileNotFound();
        } catch (Throwable $e) {
            $this->jsonRespService->generalError();
        }
        
        if ($localUrl) {
            $localThumbnail = $this->params->get('kernel.project_dir') . $this->thumbnailLocalPath . $key;               
            $this->awsService->putFile($this->thumbnailPathPrefix . $key, $this->params->get('aws.image_bucket'), $localThumbnail);
            unlink($localThumbnail);
            $res = $this->awsService->getFile(
                $this->thumbnailPathPrefix . $key, $this->params->get('aws.image_bucket')
            );
            return $res;
        }
        return false;
    }

    public function getImageLink(string $key) 
    {
        return $this->getS3Link($key, $this->imagePathPrefix);
    }

    public function getThoumnaulLink(string $key) 
    {
        return $this->getS3Link($key, $this->thumbnailPathPrefix);
    }

    public function uploadImage(string $key, $image) 
    {
        $imageRes = $this->awsService->putFile(
            $this->imagePathPrefix . $key, 
            $this->params->get('aws.image_bucket'), 
            $image->getRealPath(),
            [
                'contentType' => $image->getClientMimeType()
            ]
        );
        $thumbRes = $this->createThumbnail($key); 
        if ($imageRes && $thumbRes) {
            return true;
        }
        return false;
    }

    public function removeImage(string $key) 
    {
        $imageRes = $this->awsService->removeFile(
            $this->imagePathPrefix . $key, 
            $this->params->get('aws.image_bucket')
        );
        $thumbRes = $this->awsService->removeFile(
            $this->thumbnailPathPrefix . $key, 
            $this->params->get('aws.image_bucket')
        );
        if ($imageRes && $thumbRes) {
            return true;
        }
        return false;
    }

    public function removeBrand(string $path)
    {
        $imageRes = $this->awsService->removeMatchingFiles(
            $this->imagePathPrefix . $path, 
            $this->params->get('aws.image_bucket')
        );
        $thumbRes = $this->awsService->removeMatchingFiles(
            $this->thumbnailPathPrefix . $path, 
            $this->params->get('aws.image_bucket')
        );
        return true;
    }

    private function getS3Link(string $key, string $folderPrefix) 
    {
        return 'https://s3-'.
        $this->params->get('aws.region').
        '.amazonaws.com/'.
        $this->params->get('aws.image_bucket')."/" .
        $folderPrefix . $key;
    }
}