<?php

namespace App\Support;

use App\Models\User;

class AdminAccess
{
    public const ROLES = ['member', 'analyst', 'admin', 'super_admin'];

    private const PERMISSIONS = [
        'member' => [],
        'analyst' => [
            'admin.view', 'publishing.view', 'publishing.manage', 'sessions.view',
            'sessions.manage', 'analytics.view',
        ],
        'admin' => [
            'admin.view', 'members.view', 'members.manage', 'entitlements.manage',
            'content.view', 'content.manage', 'publishing.view', 'publishing.manage',
            'sessions.view', 'sessions.manage', 'community.view', 'community.manage',
            'analytics.view', 'audit.view',
        ],
        'super_admin' => ['*'],
    ];

    public static function allows(?User $user, string $permission): bool
    {
        if (! $user || $user->status !== 'Active') {
            return false;
        }

        $permissions = self::PERMISSIONS[$user->role ?? 'member'] ?? [];

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public static function require(User $user, string $permission): void
    {
        abort_unless(self::allows($user, $permission), 403);
    }
}
