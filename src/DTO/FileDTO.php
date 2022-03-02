<?php

namespace App\DTO;

use JsonSerializable;

class FileDTO implements JsonSerializable
{

    /**
     * @param array<Translation> $translations
     */
    public function __construct(
        private array  $translations = [],
        private string $language = 'en',
    ) {}

    public function addTranslation(Translation $translation)
    {
        $translationLocal = new Translation(
            $translation->getKey(),
            $translation->getText(),
            $this->language, // force change language
        );
        $this->translations[$translation->getKey()] = $translationLocal;
    }

    public function jsonSerialize(): array
    {
        $translationsArray = [];
        foreach ($this->translations as $key=>$translation) {
            $translationsArray[$key] = $translation->jsonSerialize();
        }

        return $translationsArray;
    }

    /**
     * @return array<Translation>
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getTranslation(string $key): ?Translation
    {
        return $this->translations[$key] ?? null;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public static function fromArray(array $data): self{

    }

}


//        [
//            'en' => [
//                'key1' => ['translation', 'comment'],
//                'key2' => '',
//                'key3' => '',45

//                // объект примитв
//            ],
//            'fr' => [],
//
//        ]
