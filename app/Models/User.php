<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
}
