<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'mysql_users';

    protected $table = 'users.users';

    protected $fillable = [
        'name',
        'email',
        'unlocked_for_qes',
        'unlocked_for_valuation',
        'unlocked_for_multi_user_license_beta',
        'parent_user_id',
        'last_login',
        'created_at',
        'updated_at',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_user_id');
    }

    public function protocols(): HasMany
    {
        return $this->hasMany(Protocol::class);
    }

    public function valuations(): HasMany
    {
        return $this->hasMany(Valuation::class);
    }

    #[Scope]
    protected function unlockedForQes(Builder $query): void
    {
        $query->where('unlocked_for_qes', true);
    }

    #[Scope]
    protected function unlockedForValuation(Builder $query): void
    {
        $query->where('unlocked_for_valuation', true);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('last_login', '>=', Carbon::now()->subDays(30));
    }

    #[Scope]
    protected function hasProtocolsInTimeRange(Builder $query, $from, $to): void
    {
        $query->whereHas('protocols', function ($query) use ($from, $to) {
            $query->whereBetween('signed_with_qes_at', [$from, $to]);
        });
    }

    #[Scope]
    protected function hasValuationsInTimeRange(Builder $query, $from, $to): void
    {
        $query->whereHas('valuations', function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        });
    }

    #[Scope]
    protected function subuser(Builder $query): void
    {
        $query->whereNotNull('parent_user_id');
    }

    #[Scope]
    protected function activeInTimeRange(Builder $query, $from, $to): void
    {
        $query->whereBetween('last_login', [$from, $to]);
    }
}
