<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

// 'role' is intentionally NOT mass-assignable — it is set explicitly only in
// trusted paths (registration → 'client', seeder/admin panel via forceFill) so
// an untrusted request can never escalate privileges through mass assignment.
#[Fillable(['name', 'email', 'password', 'phone', 'gender', 'address_line', 'city', 'postcode', 'state'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity, SoftDeletes,
        InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery;

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

    /**
     * Get the user's service bookings (those made while logged in).
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
