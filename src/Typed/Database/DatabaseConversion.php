<?php

namespace Kiwilan\Typescriptable\Typed\Database;

use BackedEnum;
use UnitEnum;

class DatabaseConversion
{
    protected function __construct(
        protected DatabaseDriver $databaseDriver,
        protected ?string $databaseType,
        protected ?string $phpType = 'mixed',
        protected string $typescriptType = 'any',
    ) {}

    /**
     * Make a new instance.
     *
     * @param  string  $databaseDriver  Database driver.
     * @param  string|null  $databaseType  Database type.
     * @param  string|null  $cast  Laravel cast.
     */
    public static function make(string $databaseDriver, ?string $databaseType, ?string $cast): self
    {
        $self = new self(
            databaseDriver: DatabaseDriver::tryFrom(strtolower($databaseDriver)),
            databaseType: $databaseType,
        );

        $self->phpType = $self->databaseDriver->toPhp($self->databaseType);
        if ($cast) {
            $self->parseCast($cast);
        } else {
            $self->typescriptType = $self->toTypescript($self->phpType);
        }

        return $self;
    }

    /**
     * Get database driver.
     */
    public function databaseDriver(): DatabaseDriver
    {
        return $this->databaseDriver;
    }

    /**
     * Get database type.
     */
    public function databaseType(): string
    {
        return $this->databaseType;
    }

    /**
     * Get PHP type.
     */
    public function phpType(): string
    {
        return $this->phpType;
    }

    /**
     * Get TypeScript type.
     */
    public function typescriptType(): string
    {
        return $this->typescriptType;
    }

    private function parseDateTimePhpCast(string $cast): self
    {
        if (! in_array($cast, ['date',
            'datetime',
            'immutable_date',
            'immutable_datetime',
            'timestamp'])) {
            return $this;
        }

        $this->phpType = '\\DateTime';

        return $this;
    }

    /**
     * Convert Laravel cast to TypeScript type.
     */
    private function parseCast(string $cast): self
    {
        if (str_contains($cast, ':')) {
            $cast = explode(':', $cast)[0];
        }

        $typescriptCastable = match ($cast) {
            'array' => 'any[]',
            'collection' => 'any[]',
            'encrypted:array' => 'any[]',
            'encrypted:collection' => 'any[]',
            'encrypted:object' => 'any',
            'object' => 'any',

            'AsStringable::class' => 'string',

            'boolean' => 'boolean',

            'date' => 'string',
            'datetime' => 'string',
            'immutable_date' => 'string',
            'immutable_datetime' => 'string',
            'timestamp' => 'string',

            'decimal' => 'number',
            'double' => 'number',
            'float' => 'number',
            'integer' => 'number',
            'int' => 'number',

            'encrypted' => 'string',
            'hashed' => 'string',
            'real' => 'string',

            'string' => 'string',

            default => 'unknown',
        };

        if ($typescriptCastable === 'unknown') {
            // enum case
            if (str_contains($cast, '\\')) {
                $this->phpType = "\\{$cast}";
                $enums = $this->parseEnum($cast);
                $this->typescriptType = $this->arrayToTypescriptTypes($enums);

                return $this;
            } else {
                // attribute or accessor case
                return $this;
            }
        }

        $this->typescriptType = $typescriptCastable;
        $this->parseDateTimePhpCast($cast);

        return $this;
    }

    /**
     * Parse enum.
     *
     * @param  string  $namespace  Enum namespace.
     * @return string[]
     */
    private function parseEnum(string $namespace): array
    {
        $reflect = new \ReflectionClass($namespace);

        $enums = [];
        $constants = $reflect->getConstants();
        $constants = array_filter($constants, fn ($value) => is_object($value));

        foreach ($constants as $name => $enum) {
            if ($enum instanceof BackedEnum) {
                $enums[$name] = $enum->value;
            } elseif ($enum instanceof UnitEnum) {
                $enums[$name] = $enum->name;
            }
        }

        return $enums;
    }

    /**
     * Convert array to TypeScript types.
     */
    private function arrayToTypescriptTypes(array $types): string
    {
        $typescript = '';

        foreach ($types as $type) {
            $typescript .= " '{$type}' |";
        }

        $typescript = rtrim($typescript, '|');

        return trim($typescript);
    }

    /**
     * Convert PHP type to TypeScript type.
     */
    private function toTypescript(string $phpType): string
    {
        return match ($phpType) {
            'string' => 'string',

            'int' => 'number',
            'integer' => 'number',
            'float' => 'number',
            'double' => 'number',

            'bool' => 'boolean',
            'boolean' => 'boolean',

            'true' => 'boolean',
            'false' => 'boolean',

            'array' => 'any[]',
            'iterable' => 'any[]',
            'object' => 'any',
            'mixed' => 'any',

            'null' => 'undefined',
            'void' => 'void',

            'resource' => 'any',

            'callable' => 'Function',

            'DateTime' => 'Date',
            'DateTimeInterface' => 'Date',
            'Carbon' => 'Date',

            'Model' => 'any',

            default => 'any', // skip `Illuminate\Database\Eloquent\Casts\Attribute`
        };
    }
}
