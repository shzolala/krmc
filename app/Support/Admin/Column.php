<?php

namespace App\Support\Admin;

class Column
{
    final protected function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type = 'text',
        public readonly bool $sortable = false,
    ) {}

    public static function text(string $name, string $label, bool $sortable = false): static
    {
        return new static($name, $label, 'text', $sortable);
    }

    public static function title(string $name, string $label, bool $sortable = true): static
    {
        return new static($name, $label, 'title', $sortable);
    }

    public static function number(string $name, string $label, bool $sortable = true): static
    {
        return new static($name, $label, 'number', $sortable);
    }

    public static function badge(string $name, string $label): static
    {
        return new static($name, $label, 'badge');
    }

    public static function toggle(string $name, string $label): static
    {
        return new static($name, $label, 'toggle');
    }

    public static function date(string $name, string $label, bool $sortable = true): static
    {
        return new static($name, $label, 'date', $sortable);
    }
}
