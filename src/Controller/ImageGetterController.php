<?php

namespace App\Controller;

use App\Dto\ImageKeyDto;
use App\Service\AWSImageService;
// use Knp\Bundle\GaufretteBundle\FilesystemMap;
// use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
// use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
// use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[Route('/images-get', name: 'images_')]
class ImageGetterController extends AbstractController
{
    public function __construct(
        private AWSImageService $awsImageService,         
    ) {}

    #[Route('/image', methods: ['GET'], name: 'image_getter')]
    public function getImage(ImageKeyDto $imageDto)
    {
        $key = $imageDto->getKey(); 
        $image = $this->awsImageService->getImage($key);

        if (!$image || isset($image['errCode'])) {
            return new Response('no image found', Response::HTTP_NOT_FOUND);
        }
        //Неправильно работает 'ContentType'
        return new Response($image['Body'], Response::HTTP_OK, [
            'Content-Type' => $image['ContentType']
        ]);
    }

    
    #[Route('/thumbnail', methods: ['GET'], name: 'thumbnail_getter')]
    public function getThumbnail(ImageKeyDto $imageDto)
    {
        $key = $imageDto->getKey(); 
        $image = $this->awsImageService->getThumbnail($key);

        if (!$image || isset($image['errCode'])) {
            return new Response('no image found', Response::HTTP_NOT_FOUND);
        }
        return new Response($image['Body'], Response::HTTP_OK, [
            'Content-Type' => $image['ContentType']
        ]);            
    }

    #[Route('/image-link', methods: ['GET'], name: 'image_link_getter')]
    public function getImageLink(ImageKeyDto $imageDto)
    {
        $key = $imageDto->getKey();               
    
        return $this->json([
            'link' => $this->awsImageService->getImageLink($key)
        ]);
    }

    #[Route('/thumbnail-link', methods: ['GET'], name: 'thumbnail_link_getter')]
    public function getThumbnailLink(ImageKeyDto $imageDto)
    {
        $key = $imageDto->getKey();               
    
        return $this->json([
            'link' => $this->awsImageService->getThoumnaulLink($key)
        ]);
    }

    private function someOldStuff() 
    {
        // https://io.serendipityhq.com/experience/how-to-use-liipimaginebundle-to-manage-thumbnails-through-amazon-s3/

        // https://detail-images.s3.eu-north-1.amazonaws.com/images/bosch/a123.jpg
        // https://s3-eu-north-1.amazonaws.com/detail-images/images/bosch/a123.jpg

        // $filesystem = $this->fileSystemMap->get('filesystem_aws_s3_images');
        // $file = $filesystem->get($key);
    }
}
