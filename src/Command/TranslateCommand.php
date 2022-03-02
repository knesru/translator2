<?php

namespace App\Command;

use App\DTO\FileDTO;
use App\DTO\FolderDTO;
use App\DTO\Translation;
use App\Enum\Format;
use App\Helper\ArrayHelper;
use App\Service\FileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranslateCommand extends Command
{

    protected const PROJECT_DIR = '..';
    protected static $defaultName = 'revizto:translate';

    public function __construct(
        private FileService $fileService
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'path',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Translation path',
            )->addOption(
                'defaultLanguage',
                'l',
                InputArgument::OPTIONAL,
                'default language',
                'en',
            )->addOption(
                'projectDir',
                'd',
                InputArgument::OPTIONAL,
                'projects\' directory',
                self::PROJECT_DIR,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = $input->getArgument('path');
        $lang = $input->getOption('defaultLanguage');
        $dir = $input->getOption('projectDir');


        foreach ($paths as $path) {
            $output->writeln($path);
            $fullDirName = $dir . DIRECTORY_SEPARATOR . $path;
            $allFiles = scandir($fullDirName);
            print_r($allFiles);
            $defaultLanguageFileName = '';
            $languageFileTemplate = '';
            $otherLanguageFiles = [];
            foreach ($allFiles as $filename) {
                $fullFileName = $fullDirName . DIRECTORY_SEPARATOR . $filename;
                //remove .gitignore .idea etc
                if (str_starts_with($filename, '.')) {
                    $output->writeln('sw . - ' . $filename);
                    continue;
                }
                //remove folders
                if (!is_file($fullFileName)) {
                    $output->writeln('not file');
                    continue;
                }

                if (self::isLanguage($filename, $lang)) {
                    $defaultLanguageFileName = $fullFileName;
                    $languageFileTemplate = str_replace('.' . $lang . '.', '\.[a-z]+\.', $filename);
                    $output->writeln("\t FN: " . $defaultLanguageFileName);
                    $output->writeln("\t PR: " . $languageFileTemplate);
                    $output->writeln("\t full: " . $fullDirName . $languageFileTemplate);
                } else {
                    $otherLanguageFiles[$fullFileName] = $filename;
                }
            }
            $output->writeln('Default language file found: ' . $defaultLanguageFileName);
            $output->writeln('Other files: ');
            foreach ($otherLanguageFiles as $languageFileFull => $languageFile) {
                $output->writeln('/' . $languageFileTemplate . '/' . $languageFile);
                if (preg_match('/' . $languageFileTemplate . '/', $languageFile)) {
                    $output->writeln($languageFileFull . ' content replaced with ' . $defaultLanguageFileName);
                    unlink($languageFileFull);
                    copy($defaultLanguageFileName, $languageFileFull);
                } else {
                    $output->writeln($languageFile . ' has wrong name format');
                }
            }

            $output->writeln('');
        }

        $transformer = $this->fileService->getTransformer('yml');

        $allTranslations = new FolderDTO();
        foreach ($otherLanguageFiles as $languageFileFull => $languageFile) {
            if($lang = self::getLanguage($languageFile)) {
                $languageData = new FileDTO(language: $lang);
                $languageArray = ArrayHelper::dot($transformer->fromFile($languageFileFull));
                foreach ($languageArray as $key => $text) {
                    $languageData->addTranslation(new Translation($key, $text ?? ''));
                }
                $allTranslations->addFile($languageData);
            }
        }

        print_r($allTranslations->jsonSerialize());

//        [
//            'folder' => [
//                'path' => '',
//                'files' => [
//                    'en' => [
//                        'key1' => ['translation', 'comment'],
//                        'key2' => '',
//                        'key3' => '',
//                        // объект примитв
//                    ],
//                    'fr' => [],
//
//                ],
//                'type' => 'table|file'
//            ]
//        ];

        // abc.aaa.aa <=>
        // abc:


        // заполнение объекта
        // найти какие ключи из первого фолдера не встречаются ни в одном из n фолдеров (удаление из текущих)
        // добавление массива ключей в один из фолдеров
        // удаление массива ключей в один из фолдеров
        // синк объекта с гуглом и с файлами

        return Command::SUCCESS;
    }

    private static function getFormat(string $filename): string
    {
        foreach (Format::cases() as $format) {
            if (str_ends_with($filename, '.' . $format->value)) {
                return $format->value;
            }
        }

        return '';
    }

    private static function isLanguage(string $filename, string $language): bool
    {
        return str_contains($filename, '.' . $language . '.');
    }

    private static function getLanguage(string $filename): null|string
    {
        if (preg_match('/.(\w{2})\.yml/',$filename,$matches)) {
            return $matches[1];
        }
        return null;
    }
}