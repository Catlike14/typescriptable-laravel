<?php

namespace Kiwilan\Typescriptable\Eloquent\Utils\Schema;

class SchemaCollection
{
    /**
     * @param  string[]  $skip
     * @param  SchemaClass[]  $items
     */
    protected function __construct(
        protected string $basePath,
        protected array $skip = [],
        protected array $items = [],
    ) {}

    /**
     * @param  string[]  $skip
     */
    public static function make(string $basePath, array $skip = []): self
    {
        $self = new self($basePath, $skip);

        /** @var SchemaClass[] */
        $items = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isDir()) {
                $model = SchemaClass::make($file, $basePath);
                if (! $model) {
                    continue;
                }

                $items[$model->getFullname()] = $model;
            }
        }

        $self->items = $self->skipNamespace($items, $self->skip);

        return $self;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return SchemaClass[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return SchemaClass[]
     */
    public function getOnlyModels(): array
    {
        return array_filter($this->items, fn (SchemaClass $item) => $item->isModel());
    }

    /**
     * @param  SchemaClass[]  $classes
     * @param  string[]  $skip
     */
    private function skipNamespace(array $classes, array $skip): array
    {
        return array_filter($classes, fn (SchemaClass $item) => ! in_array($item->getNamespace(), $skip));
    }
}
