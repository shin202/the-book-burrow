<?php

namespace App\Models;

use App\Builder\UserBuilder;
use App\Enums\AccountStatusEnum;
use App\Traits\Configurable;
use App\Traits\UpdateOnlyColumn;
use App\Traits\UserRoleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, UserRoleTrait, HasDatabaseNotifications, UpdateOnlyColumn;
    use Billable, Configurable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
        'email_verified_at',
    ];

    protected $casts = [
        'settings' => 'json'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at) && $this->status === AccountStatusEnum::ACTIVE;
    }

    public function markEmailAsVerified(): void
    {
        $this->email_verified_at = now();
        $this->status = AccountStatusEnum::ACTIVE;
        $this->save();
    }

    public function createEmailVerifyToken(): EmailVerifyToken
    {
        return $this->emailVerifyTokens()->create([
            'token' => Str::random(32)
        ]);
    }

    public function emailVerifyTokens(): HasMany
    {
        return $this->hasMany(EmailVerifyToken::class);
    }

    public function updateAvatar(string $path): void
    {
        $this->avatar = $path;
        $this->save();
    }

    public function upsertPersonalInformation(array $data): void
    {
        $this->profile()->updateOrCreate([
            'user_id' => $this->id
        ], $data);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_usages', 'user_id', 'coupon_id')->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => AccountStatusEnum::class,
        ];
    }
}
