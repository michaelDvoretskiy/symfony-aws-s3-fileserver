<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class BrandKeyDto extends AbstractJsonRequest
{
    #[Assert\NotBlank(message: 'path parameter should be passed')]
    public readonly string $path;

    public function getPath()
    {
        return $this->path . "/";
    }
}