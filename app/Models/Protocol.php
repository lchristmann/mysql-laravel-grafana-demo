<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Protocol extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'app.protocols';

    protected $fillable = [
        'title',
        'signed_with_qes_at',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function signedWithQES(Builder $query): void
    {
        $query->whereNotNull('signed_with_qes_at');
    }

    #[Scope]
    protected function signedWithQESInTimeRange(Builder $query, $from, $to): void
    {
        $query->whereBetween('signed_with_qes_at', [$from, $to]);
    }

    #[Scope]
    protected function subuser(Builder $query): void
    {
        $query->whereHas('user', function ($query) {
            $query->whereNotNull('parent_user_id');
        });
    }
}
