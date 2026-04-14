<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Cart;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postal_code',
        'role',
        'api_token',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

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
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Boot - handle is_admin to role conversion and create cart
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Convert is_admin to role
            if (isset($model->attributes['is_admin']) && $model->attributes['is_admin']) {
                $model->role = 'admin';
            } elseif (!isset($model->role) || !$model->role) {
                $model->role = 'user';
            }
        });

        static::created(function ($model) {
            // Create cart for user if it doesn't exist
            if (!$model->cart) {
                Cart::create(['user_id' => $model->id]);
            }
        });

        static::updating(function ($model) {
            // Convert is_admin to role on update
            if ($model->isDirty('is_admin')) {
                $model->role = $model->attributes['is_admin'] ? 'admin' : 'user';
            }
        });
    }

    /**
     * Generate a new API token for the user.
     */
    public function generateToken(): string
    {
        $token = \Illuminate\Support\Str::random(80);
        $this->api_token = $token;
        $this->save();
        return $token;
    }

    /**
     * Get the cart for the user.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the reviews for the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
