<?php

namespace Kiwilan\Typescriptable\Commands;

use Illuminate\Console\Command;
use Kiwilan\Typescriptable\Typescriptable;

class TypescriptableEloquentCommand extends Command
{
    public $signature = 'typescriptable:eloquent';

    public $description = 'Generate Eloquent models types.';

    protected function configure()
    {
        $this->setAliases([
            'typescriptable:models',
        ]);

        parent::configure();
    }

    public function handle(): int
    {
        $service = Typescriptable::getModelsPath();
        $namespaces = [];

        foreach ($service->app()->getModelsPath() as $model) {
            $namespaces[] = [$model->schemaClass()->namespace()];
        }
        $this->table(['Models'], $namespaces);

        $this->info('Generated model types.');

        return self::SUCCESS;
    }
}
