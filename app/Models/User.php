<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\MemberEntitlement;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar_url',
        'current_path',
        'status',
        'role',
        'timezone',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Send the OHC account-verification notification synchronously.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\OHCVerifyEmail());
    }

    public function entitlements()
    {
        return $this->hasMany(MemberEntitlement::class);
    }

    public function hasCourseAccess(string $courseKey): bool
    {
        $course = config('ohc_access.courses.'.$courseKey);

        if (! $course) {
            return false;
        }

        if ($this->hasMatchingActiveEntitlement($course['external_references'] ?? [], 'external_reference')) {
            return true;
        }

        if ($this->hasMatchingActiveEntitlement($course['product_names'] ?? [], 'product_name')) {
            return true;
        }

        if ($this->hasMatchingActiveEntitlement($course['offer_names'] ?? [], 'offer_name')) {
            return true;
        }

        foreach (($course['included_by'] ?? []) as $bundleName) {
            if ($this->hasActiveBundle($bundleName)) {
                return true;
            }
        }

        return false;
    }

    public function hasActiveBundle(string $bundleName): bool
    {
        if ($this->current_path === $bundleName) {
            return true;
        }

        return $this->activeEntitlements()
            ->contains(function (MemberEntitlement $entitlement) use ($bundleName) {
                return $entitlement->product_name === $bundleName
                    || $entitlement->offer_name === $bundleName
                    || $entitlement->offer_name === $bundleName.' Path';
            });
    }

    /**
     * Backward-compatible wrapper for older checks.
     */
    public function hasAccessTo($productNameOrPath)
    {
        if (config('ohc_access.courses.'.$productNameOrPath)) {
            return $this->hasCourseAccess($productNameOrPath);
        }

        if (array_key_exists($productNameOrPath, config('ohc_access.bundles', []))) {
            return $this->hasActiveBundle($productNameOrPath);
        }

        return $this->hasMatchingActiveEntitlement([$productNameOrPath], 'product_name')
            || $this->hasMatchingActiveEntitlement([$productNameOrPath], 'offer_name');
    }

    private function activeEntitlements()
    {
        $entitlements = $this->relationLoaded('entitlements')
            ? $this->entitlements
            : $this->entitlements()->get();

        return $entitlements->filter(function (MemberEntitlement $entitlement) {
            return $entitlement->status === 'Active'
                && (is_null($entitlement->expires_at) || $entitlement->expires_at->isFuture());
        });
    }

    private function hasMatchingActiveEntitlement(array $values, string $field): bool
    {
        $values = array_filter($values);

        if ($values === []) {
            return false;
        }

        return $this->activeEntitlements()
            ->contains(fn (MemberEntitlement $entitlement) => in_array($entitlement->{$field}, $values, true));
    }

    public function courseProgress()
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function weeklyGoals()
    {
        return $this->hasMany(WeeklyGoal::class);
    }


    public function canAdmin(string $permission): bool
    {
        return \App\Support\AdminAccess::allows($this, $permission);
    }

    public function isAdmin(): bool
    {
        return $this->canAdmin('admin.view');
    }

}