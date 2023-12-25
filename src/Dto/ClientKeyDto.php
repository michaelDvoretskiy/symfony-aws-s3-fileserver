<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ClientKeyDto extends AbstractJsonRequest
{
    #[Assert\NotBlank(message: 'clientId parameter should be passed')]
    public readonly string $clientId;

    public function getPath()
    {
        return $this->clientId . "/";
    }
}