<?php

namespace App\Controller;

use App\Dto\BrandKeyDto;
use App\Dto\ImageKeyDto;
use App\Service\AWSImageService;
use App\Service\JsonResponsesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/images-upload', name: 'images_upload_')]
class ImageUploadController extends AbstractController
{
    public function __construct(
        private AWSImageService $awsImageService,
        private JsonResponsesService $jsonRespService
    ) {}

    #[Route('/image', methods: ['POST'], name: 'image')]
    public function uploadImage(Request $request, ImageKeyDto $imageDto)
    {
        $key = $imageDto->getKey();
        $file = $request->files->get('image');
        $res = $this->awsImageService->uploadImage($key, $file); 

        return $this->jsonRespService->success(['success' => $res]);
    }

    #[Route('/remove-image', methods: ['DELETE'], name: 'remove_image')]
    public function removeImage(ImageKeyDto $imageDto)
    {
        $key = $imageDto->getKey();
        $res = $this->awsImageService->removeImage($key); 

        return $this->jsonRespService->success(['success' => $res]);
    }

    #[Route('/remove-brand', methods: ['DELETE'], name: 'remove_brand')]
    public function removeBrandImages(BrandKeyDto $brandDto)
    {
        $key = $brandDto->getPath();
        $res = $this->awsImageService->removeBrand($key); 

        return $this->jsonRespService->success(['success' => $res]);
    }
}
