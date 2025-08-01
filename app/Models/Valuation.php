<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valuation extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'app.valuations';

    protected $fillable = [
        'price_in_cents',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function createdInTimeRange(Builder $query, $from, $to): void
    {
        $query->whereBetween('created_at', [$from, $to]);
    }
}
