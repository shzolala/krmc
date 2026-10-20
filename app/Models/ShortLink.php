<?php

namespace App\Models;

use App\Support\MapLink;
use Database\Factories\ShortLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['code', 'query_hash', 'query', 'created_by'])]
class ShortLink extends Model
{
    /** @use HasFactory<ShortLinkFactory> */
    use HasFactory;

    /**
     * Returns the existing short link for these map options, or creates one. Equal options always share a code.
     *
     * @param  array<string, mixed>  $options  Sanitized MapLink options
     */
    public static function forMapOptions(array $options, ?int $userId = null): self
    {
        $query = (string) parse_url(MapLink::url($options), PHP_URL_QUERY);

        return self::query()->firstOrCreate(
            ['query_hash' => sha1($query)],
            ['code' => self::freshCode(), 'query' => $query, 'created_by' => $userId],
        );
    }

    public function shortUrl(): string
    {
        return route('map.short', $this->code);
    }

    public function targetUrl(): string
    {
        return route('map').($this->query === '' ? '' : '?'.$this->query);
    }

    private static function freshCode(): string
    {
        do {
            $code = Str::lower(Str::random(6));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
