<?php

namespace App\Support\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Carbon;

/**
 * Convertit une colonne PostgreSQL `daterange` vers un tableau
 * `['start' => Carbon, 'end' => Carbon]` et inversement.
 *
 * Respecte la notation par intervalle demi-ouvert `[start,end)` — borne
 * basse incluse, borne haute exclue — conformément à la convention de
 * journée définie dans BUSINESS_RULES.md §3.1 et §3.4 (12h00 à 12h00,
 * le jour de départ n'étant jamais facturé comme journée pleine).
 *
 * @implements CastsAttributes<array{start: \Illuminate\Support\Carbon, end: \Illuminate\Support\Carbon}, array{start: \DateTimeInterface|string, end: \DateTimeInterface|string}>
 */
class PostgresDateRange implements CastsAttributes
{
    /**
     * {@inheritDoc}
     */
    public function get($model, string $key, $value, array $attributes): ?array
    {
        if ($value === null) {
            return null;
        }

        if (! preg_match('/^[\[(](\d{4}-\d{2}-\d{2}),(\d{4}-\d{2}-\d{2})[\])]$/', (string) $value, $matches)) {
            return null;
        }

        return [
            'start' => Carbon::parse($matches[1])->startOfDay(),
            'end' => Carbon::parse($matches[2])->startOfDay(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        $start = Carbon::parse($value['start'])->toDateString();
        $end = Carbon::parse($value['end'])->toDateString();

        return "[{$start},{$end})";
    }
}
