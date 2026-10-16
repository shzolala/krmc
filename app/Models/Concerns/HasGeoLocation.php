<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasGeoLocation
{
    /**
     * @return array<string, string>
     */
    protected function geoLocationCasts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>|null
     */
    public function toMapMarker(string $type, ?string $url = null, array $extra = []): ?array
    {
        if (! $this->hasCoordinates()) {
            return null;
        }

        return [
            'id' => $type.'-'.$this->getKey(),
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
            'label' => (string) ($this->name ?? $this->title ?? 'مکان'),
            'type' => $type,
            'url' => $url,
            ...$extra,
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }
}
