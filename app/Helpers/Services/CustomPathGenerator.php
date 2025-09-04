<?php

namespace App\Helpers\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator as BasePathGenerator;

class CustomPathGenerator implements BasePathGenerator
{
    public function getPath(Media $media): string
    {
        return substr(strrchr($media->model_type, '\\'), 1) .
            DIRECTORY_SEPARATOR .
            $media->uuid .
            DIRECTORY_SEPARATOR;
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions' . DIRECTORY_SEPARATOR;
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive-images' . DIRECTORY_SEPARATOR;
    }
}
