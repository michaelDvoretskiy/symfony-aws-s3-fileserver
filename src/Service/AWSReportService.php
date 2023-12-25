<?php

namespace App\Service;

use App\Service\AWSService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class AWSReportService
{
    public function __construct(
        private AWSService $awsService, 
        private ContainerBagInterface $params,
    ) {}

    public function getReport(string $key) 
    {
        return $this->awsService->getFile(
            $key, $this->params->get('aws.report_bucket')
        );
    }

    public function uploadReport(string $key, $report)
    {
        return $this->awsService->putFile(
            $key, $this->params->get('aws.report_bucket'), 
            $report->getRealPath(),
            [
                'contentType' => $report->getClientMimeType()
            ]
        );
    }

    public function removeReport(string $key) 
    {
        $res = $this->awsService->removeFile(
            $key, 
            $this->params->get('aws.report_bucket')
        );
        if ($res) {
            return true;
        }
        return false;
    }

    public function removeClient(string $path)
    {
        $res = $this->awsService->removeMatchingFiles(
            $path, 
            $this->params->get('aws.report_bucket')
        );

        return true;
    }
}