<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ReportKeyDto extends AbstractJsonRequest
{
    #[Assert\NotBlank(message: 'clientId parameter should be passed')]
    public readonly string $clientId;
    #[Assert\NotBlank(message: 'reportType parameter should be passed')]
    public readonly string $reportType;
    #[Assert\NotBlank(message: 'fileName parameter should be passed')]
    public readonly string $fileName;

    public function getKey()
    {
        return $this->clientId . "/" . $this->reportType . "/" . $this->fileName;
    }
}