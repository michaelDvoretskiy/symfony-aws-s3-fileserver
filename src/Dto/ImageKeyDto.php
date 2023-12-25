<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ImageKeyDto extends AbstractJsonRequest
{
    #[Assert\NotBlank(message: 'file parameter should be passed')]
    public readonly string $file;
    #[Assert\NotBlank(message: 'path parameter should be passed')]
    public readonly string $path;

    public function getKey()
    {
        return $this->path . "/" . $this->file;
    }
}