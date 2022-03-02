<?php

namespace App\Transformers;

use Revizto\TranslatorBundle\Helper\ArrayHelper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class JsTransformer
 * @package Revizto\TranslatorBundle\Transformers
 *
 * @author  Sergey Koksharov <s.koksharov@revizto.com>
 */
class JsTransformer implements TransformerInterface
{
    /**
     * @param string $filePath
     *
     * @return array
     */
    public function fromFile(string $filePath): array
    {
        $fileContent = file_get_contents($filePath);

        if (!preg_match('/var (?P<var>.*) = (?P<content>.*)/Umsi', $fileContent, $matches)) {
            throw new \LogicException(sprintf('Unable to parse JS file without any variables %s', $filePath));
        }

        $ex = explode($matches[0], $fileContent, 2);

        $content = end($ex);

        // делаем все ключи с кавычками
        $content = preg_replace('/^(\s+)([a-zA-Z0-9_-]+)\s*:(.*)$/Umsi', '$1"$2":$3', $content);
        // преобразовываем одинарные кавычки в двойные для ключей
        $content = preg_replace('/^(\s+)\'([a-zA-Z0-9_-]+)\'\s*:(.*)$/Umsi', '$1"$2":$3', $content);
        // преобразуем все кавычки в двойные, по стандарту JSON
        $content = preg_replace_callback(
            '/^(\s+)\"([a-zA-Z0-9_-]+)\":\s*\'(.*)\'([,\s]*)$/Umsi',
            function ($matches) {
                return sprintf(
                    '%s"%s": "%s"%s',
                    $matches[1],
                    $matches[2],
                    addcslashes(stripslashes($matches[3]), '"'),
                    $matches[4]
                );
            },
            $content
        );
        // удаляем лишние запятые
        $content = preg_replace('/\,(\s*\})/Umsi', '$1', $content);

        $content = trim(trim($content), ';');

        $result = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('[' . json_last_error() . '] ' . $filePath . ' Unable to parse content: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * @param array  $data
     * @param string $filePath
     *
     * @return bool|int
     */
    public function save(array $data, string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \LogicException('For JS needs to exist endpoint file');
        }

        if (0 === filesize($filePath)) {
            file_put_contents($filePath, 'var tmp = {}');
        }

        $fileContent = file_get_contents($filePath);

        if (!preg_match('/var (?P<var>.*) = (?P<content>.*)/Umsi', $fileContent, $matches)) {
            throw new \LogicException(sprintf('Unable to parse JS file without any variables %s', $filePath));
        }

        $ex = explode($matches[0], $fileContent, 2);
        $comments = current($ex);
        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $content = preg_replace('/^(\s+)"(\b[a-zA-Z][a-zA-Z_0-9]+)":(.*)$/Umsi', '$1$2:$3', $content);

        return file_put_contents($filePath, sprintf('%svar %s = %s;', $comments, $matches['var'], $content));
    }
}
