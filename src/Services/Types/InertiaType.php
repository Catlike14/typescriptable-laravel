<?php

namespace Kiwilan\Typescriptable\Services\Types;

use Illuminate\Support\Facades\File;
use Kiwilan\Typescriptable\Commands\TypescriptableInertiaCommand;
use Kiwilan\Typescriptable\Services\Types\Inertia\InertiaEmbed;
use Kiwilan\Typescriptable\Services\Types\Inertia\InertiaPage;
use Kiwilan\Typescriptable\TypescriptableConfig;

class InertiaType
{
    protected function __construct(
      public string $filePath,
      public bool $skipPage = true,
      public bool $useEmbed = false,
    ) {
    }

    public static function make(TypescriptableInertiaCommand $command): self
    {
        $path = TypescriptableConfig::outputPath();
        $filename = TypescriptableConfig::filenameInertia();

        $file = "{$path}/{$filename}";
        $service = new self(
            filePath: $file,
            skipPage: $command->skipPage,
            useEmbed: $command->useEmbed,
        );
        $generatedRoutes = $service->generate();

        if (! File::isDirectory($path)) {
            File::makeDirectory($filename);
        }
        File::put($file, $generatedRoutes);

        return $service;
    }

    private function generate(): string
    {
        $page = $this->skipPage ? '' : InertiaPage::make();
        $useEmbed = $this->useEmbed ? InertiaEmbed::make() : InertiaEmbed::native();

        return <<<typescript
// This file is auto generated by TypescriptableLaravel.
declare namespace Inertia {
  {$page}
}

{$useEmbed}

export {};
typescript;
    }
}
