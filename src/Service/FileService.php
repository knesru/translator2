<?php

namespace App\Service;

use App\Transformers\JsTransformer;
use App\Transformers\TransformerInterface;
use App\Transformers\YmlTransformer;

class FileService
{
    /**
     * @param string $format
     *
     * @return TransformerInterface
     */
    public function getTransformer(string $format): TransformerInterface
    {
        return match ($format) {
            'yml', 'yaml' => new YmlTransformer(),
            'js', 'ts' => new JsTransformer(),
            default => throw new \RuntimeException(sprintf('Unable to load "%s" transformer', $format)),
        };
    }
}