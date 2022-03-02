<?php

namespace App\Transformers;

/**
 * Class TransformerInterface
 * @package Revizto\TranslatorBundle\Transformers
 *
 * @author  Sergey Koksharov <sharoff45@gmail.com>
 */
interface TransformerInterface
{

    /**
     * Get translations array from file
     *
     * @param string $filePath
     *
     * @return mixed
     */
    public function fromFile(string $filePath);

    /**
     * Save array of translations to file
     *
     * @param array  $data
     * @param string $filePath
     *
     * @return mixed
     */
    public function save(array $data, string $filePath);
}
