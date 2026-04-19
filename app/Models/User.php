<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'gender', 'address_line', 'city', 'postcode', 'state', 'preferred_courier'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->dontLogIfAttributesChangedOnly(['remember_token']);
    }

    /**
     * Get the attributes that should be cast.
     *
     * CYBERSECURITY: The 'hashed' cast ensures that any value
     * assigned to 'password' is automatically hashed using bcrypt
     * before being stored in the database. This prevents plaintext
     * password storage under all circumstances.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user can access the admin panel.
     * Owner, admin, and staff can access.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if the user has admin role (includes owner).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'owner';
    }

    /**
     * Check if the user has staff role.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if the user is a client.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
