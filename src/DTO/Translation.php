<?php

namespace App\DTO;

use JsonSerializable;

class Translation implements JsonSerializable
{

    public function __construct(
        private string $key,
        private string $text,
        private string $language = 'en',
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'key'=>$this->key,
            'text'=>$this->text,
            'language'=>$this->language,
        ];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getText(): string
    {
        return $this->text;
    }

}