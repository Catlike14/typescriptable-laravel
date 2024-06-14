<?php

namespace Kiwilan\Typescriptable\Typed\Eloquent\Output;

use Illuminate\Support\Facades\File;
use Kiwilan\Typescriptable\Typed\Eloquent\Utils\EloquentProperty;

class EloquentPhp
{
    /** @var array<string,string> */
    public array $content;

    protected function __construct(
        public string $path,
    ) {
    }

    /**
     * @param  array<string,EloquentProperty[] | array<string,EloquentProperty[]>>  $eloquents
     */
    public static function make(array $eloquents, string $path): self
    {
        $self = new self($path);

        foreach ($eloquents as $model => $eloquent) {
            $content = [];

            $content[] = '<?php';
            $content[] = '';
            $content[] = 'namespace App\Types;';
            $content[] = '';
            $content[] = '// This file is auto generated by TypescriptableLaravel.';
            $content[] = "class {$model}";
            $content[] = '{';

            $count = count($eloquent);
            $i = 0;
            foreach ($eloquent as $property) {
                if (is_array($property)) {
                    // foreach ($property as $p) {
                    //     $self->scanProperty($p, $i, $count, $content);
                    // }
                    // pivot
                } else {
                    $self->scanProperty($property, $i, $count, $content);
                }
            }

            $content[] = '}';
            $content[] = '';

            $self->content["{$model}.php"] = implode(PHP_EOL, $content);
        }

        return $self;
    }

    private function scanProperty(EloquentProperty $property, int &$i, int $count, array &$content)
    {
        $name = $property->name();
        $type = $property->phpType();

        $i++;
        $isLast = $i === $count;
        $isPrimitive = $this->isPrimitive($type);

        if ($isPrimitive) {
            $content[] = $this->setField($property, $isLast);

            return;
        }

        if ($property->isArray()) {
            $content[] = $this->setField($property, $isLast);

            return;
        }

        $type = "\\{$type}";
        $content[] = $this->setField($property, $isLast);
    }

    public function print(bool $delete = false): void
    {
        if (! File::exists($this->path)) {
            File::makeDirectory($this->path);
        }

        foreach ($this->content as $name => $content) {
            $path = "{$this->path}/{$name}";
            if ($delete) {
                File::delete($path);
            }

            File::put($path, $content);
        }
    }

    private function setField(EloquentProperty $property, bool $isLast): string
    {
        $comment = null;
        $field = '';
        $type = $property->phpType();

        if ($property->isArray()) {
            if (str_contains($type, '[]')) {
                $type = str_replace('[]', '', $type);
            }
            $comment = '    /** @var '.$type.'[] */';
            $type = 'array';
        }

        if ($comment) {
            $field = $comment.PHP_EOL;
        }

        if ($this->isDateTime($type)) {
            $type = 'DateTime';
        }

        if ($this->isClass($type)) {
            $type = "\\{$type}";
        }

        if ($property->isNullable() && $property->phpType() !== 'mixed') {
            $type = "?{$type}";
        }

        $field .= "    public {$type} \${$property->name()};";
        if (! $isLast) {
            $field .= PHP_EOL;
        }

        $field = str_replace('\\\\', '\\', $field);

        return $field;
    }

    private function isClass(string $type): bool
    {
        return class_exists($type);
    }

    private function isDateTime(string $type): bool
    {
        return $type === 'DateTime' || $type === 'datetime';
    }

    private function isPrimitive(string $type): bool
    {
        return in_array($type, [
            'int',
            'string',
            'bool',
            'float',
            'array',
            'mixed',
            'object',
            'null',
            'callable',
            'iterable',
            'resource',
            'void',
            'never',
            'false',
            'true',
            'self',
            'static',
            'parent',
        ]);
    }
}
