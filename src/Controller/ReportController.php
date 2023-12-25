<?php

namespace App\Controller;

use App\Dto\ClientKeyDto;
use App\Dto\ReportKeyDto;
use App\Service\AWSReportService;
use App\Service\JsonResponsesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Knp\Bundle\GaufretteBundle\FilesystemMap;
// use Liip\ImagineBundle\Service\FilterService;
// use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/reports', name: 'reports_')]
class ReportController extends AbstractController
{
    public function __construct(
        private AWSReportService $awsReportService,
        private JsonResponsesService $jsonRespService
    ) {}

    #[Route('/get/report', methods: ['GET'], name: 'report_getter')]
    public function getReport(ReportKeyDto $reportDto)
    {
        $key = $reportDto->getKey(); 
        $image = $this->awsReportService->getReport($key);

        if (!$image || isset($image['errCode'])) {
            return new Response('no file found', Response::HTTP_NOT_FOUND);
        }
        return new Response($image['Body'], Response::HTTP_OK, [
            'Content-Type' => $image['ContentType']
        ]);
    }    

    #[Route('/upload/report', methods: ['POST'], name: 'report_upload')]
    public function uploadReport(Request $request, ReportKeyDto $reportDto)
    {
        $key = $reportDto->getKey();   
        $file = $request->files->get('report');
        $res = $this->awsReportService->uploadReport($key, $file); 
        return $this->jsonRespService->success(['success' => $res == true]);
    }

    #[Route('/remove/report', methods: ['DELETE'], name: 'report_remove')]
    public function removeReport(ReportKeyDto $reportDto)
    {
        $key = $reportDto->getKey();  
        $res = $this->awsReportService->removeReport($key); 
        return $this->jsonRespService->success(['success' => $res == true]);
    }

    #[Route('/remove/client', methods: ['DELETE'], name: 'client_remove')]
    public function removeClientReports(ClientKeyDto $clientDto)
    {
        $path = $clientDto->getPath();   
        $res = $this->awsReportService->removeClient($path); 
        return $this->jsonRespService->success(['success' => $res == true]);
    }
}
