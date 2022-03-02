<?php

namespace App\Transformers;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YmlTransformer
 * @package Revizto\TranslatorBundle\Transformers
 *
 * @author  Sergey Koksharov <sharoff45@gmail.com>
 */
class YmlTransformer implements TransformerInterface
{

    /**
     * @param string $filePath
     *
     * @return array
     */
    public function fromFile(string $filePath): array
    {
        return Yaml::parseFile($filePath);
    }

    /**
     * @param array  $data
     * @param string $filePath
     *
     * @return bool|int
     */
    public function save(array $data, string $filePath)
    {
        return file_put_contents($filePath, $this->formatted($data));
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function formatted(array $data): string
    {
        $flags = Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_OBJECT;

        return Yaml::dump($data, 10, 4, $flags);
    }
}
