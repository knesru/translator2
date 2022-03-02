<?php

namespace App\DTO;

use JsonSerializable;

class FolderDTO implements JsonSerializable
{
    /** @param array<FileDTO> $files */
    public function __construct(private array $files = []) {}

    public function addFile(FileDTO $file): void
    {
        $this->files[] = $file;
    }

    public function jsonSerialize(): mixed
    {
        $filesArray = [];
        foreach ($this->files as $file) {
            $filesArray[$file->getLanguage()] = $file->jsonSerialize();
        }

        return $filesArray;
    }

    public function getFile(string $language): ?FileDTO{
        return $this->files[$language] ?? null;
    }

    public function getTranslation(string $language, string $key): ?Translation{
        return $this?->getFile($language)?->getTranslation($key);
    }

    /** @return array<Translation> */
    public function getTranslationsByKey(string $key): array{
        $translations = [];
        foreach ($this->files as $file){
            $translations[$file->getLanguage()] = $file->getTranslation($key);
        }
        return $translations;
    }

    /** @return array<Translation> */
    public function getTranslationsByLanguage(string $language): array{
        if($this->getFile($language)){
            return $this->getFile($language)->getTranslations();
        }
        return [];
    }
}



//[
//    'folder' => [
//        'path' => '',
//        'files' => [
//            'en' => [
//                'key1' => ['translation', 'comment'],
//                'key2' => '',
//                'key3' => '',
//                // объект примитв
//            ],
//            'fr' => [],
//
//        ],
//        'type' => 'table|file'
//    ]
//];