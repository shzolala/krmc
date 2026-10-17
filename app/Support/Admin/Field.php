<?php

namespace App\Support\Admin;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Field
{
    /**
     * @var list<mixed>
     */
    protected array $extraRules = [];

    protected bool $required = false;

    protected bool $unique = false;

    protected ?string $hint = null;

    protected bool $wide = false;

    protected ?Closure $autoValue = null;

    protected mixed $default = null;

    /**
     * @param  array<string, string>  $options
     * @param  class-string<Model>|null  $relatedModel
     */
    final protected function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        protected array $options = [],
        protected ?string $relatedModel = null,
        protected string $relatedLabel = 'name',
        protected ?int $max = null,
    ) {}

    public static function text(string $name, string $label, int $max = 255): static
    {
        return new static($name, $label, 'text', max: $max);
    }

    public static function textarea(string $name, string $label, int $max = 65000): static
    {
        return (new static($name, $label, 'textarea', max: $max))->wide();
    }

    public static function number(string $name, string $label): static
    {
        return new static($name, $label, 'number');
    }

    public static function decimal(string $name, string $label): static
    {
        return new static($name, $label, 'decimal');
    }

    public static function toggle(string $name, string $label): static
    {
        return new static($name, $label, 'toggle');
    }

    public static function datetime(string $name, string $label): static
    {
        return new static($name, $label, 'datetime');
    }

    /**
     * @param  array<string, string>  $options
     */
    public static function select(string $name, string $label, array $options): static
    {
        return new static($name, $label, 'select', options: $options);
    }

    /**
     * @param  class-string<Model>  $model
     */
    public static function relation(string $name, string $label, string $model, string $labelColumn = 'name'): static
    {
        return new static($name, $label, 'relation', relatedModel: $model, relatedLabel: $labelColumn);
    }

    public static function tags(string $name, string $label): static
    {
        return (new static($name, $label, 'tags'))->hint('هر مورد در یک خط یا با ویرگول جدا شود.');
    }

    public static function json(string $name, string $label): static
    {
        return (new static($name, $label, 'json'))->wide()->hint('ساختار JSON معتبر وارد کنید.');
    }

    public function required(): static
    {
        $this->required = true;

        return $this;
    }

    public function unique(): static
    {
        $this->unique = true;

        return $this;
    }

    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function wide(): static
    {
        $this->wide = true;

        return $this;
    }

    /**
     * @param  list<mixed>  $rules
     */
    public function rules(array $rules): static
    {
        $this->extraRules = $rules;

        return $this;
    }

    /**
     * Generates a value on create when the field was left blank.
     *
     * @param  Closure(array<string, mixed>): ?string  $generator
     */
    public function autoValue(Closure $generator): static
    {
        $this->autoValue = $generator;

        return $this;
    }

    /**
     * Value stored when the input is left blank (for NOT NULL columns).
     */
    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isWide(): bool
    {
        return $this->wide;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function hasAutoValue(): bool
    {
        return $this->autoValue !== null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function generate(array $data): ?string
    {
        return $this->autoValue ? ($this->autoValue)($data) : null;
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        if ($this->type === 'relation') {
            $model = $this->relatedModel;

            return $model::query()
                ->orderBy($this->relatedLabel)
                ->limit(1000)
                ->pluck($this->relatedLabel, 'id')
                ->mapWithKeys(fn ($label, $id): array => [(string) $id => (string) $label])
                ->all();
        }

        return $this->options;
    }

    /**
     * @return list<mixed>
     */
    public function validationRules(string $table, ?Model $record): array
    {
        $rules = [$this->required ? 'required' : 'nullable'];

        $rules = [...$rules, ...match ($this->type) {
            'text' => ['string', 'max:'.$this->max],
            'textarea' => ['string', 'max:'.$this->max],
            'number' => ['integer', 'min:0'],
            'decimal' => ['numeric'],
            'toggle' => ['boolean'],
            'datetime' => ['date'],
            'select' => [Rule::in(array_keys($this->options))],
            'relation' => ['integer', Rule::exists((new $this->relatedModel)->getTable(), 'id')],
            'tags' => ['string', 'max:5000'],
            'json' => ['json'],
            default => [],
        }];

        if ($this->unique) {
            $rules[] = Rule::unique($table, $this->name)->ignore($record?->getKey());
        }

        return [...$rules, ...$this->extraRules];
    }

    public function formValue(?Model $record): mixed
    {
        $value = $record?->getAttribute($this->name);

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return match ($this->type) {
            'tags' => is_array($value) ? implode("\n", $value) : '',
            'json' => $value === null ? '' : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'datetime' => $value?->format('Y-m-d\TH:i'),
            'toggle' => (bool) $value,
            default => $value,
        };
    }

    public function castInput(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $this->type === 'toggle' ? false : $this->default;
        }

        return match ($this->type) {
            'number', 'relation' => (int) $value,
            'decimal' => (float) $value,
            'toggle' => (bool) $value,
            'tags' => collect(preg_split('/[\r\n,،]+/u', (string) $value))
                ->map(fn (string $item): string => trim($item))
                ->filter()
                ->values()
                ->all(),
            'json' => json_decode((string) $value, true),
            default => $value,
        };
    }
}
