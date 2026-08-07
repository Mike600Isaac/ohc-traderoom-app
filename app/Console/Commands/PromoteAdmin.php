<?php

namespace App\Console\Commands;

use App\Models\AdminAuditLog;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Console\Command;

class PromoteAdmin extends Command
{
    protected $signature = 'admin:promote {email} {--role=super_admin}';
    protected $description = 'Assign an OHC staff role to an existing user';

    public function handle(): int
    {
        $role = (string) $this->option('role');
        if (! in_array($role, AdminAccess::ROLES, true) || $role === 'member') {
            $this->error('Role must be analyst, admin, or super_admin.');
            return self::FAILURE;
        }

        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->error('No user exists with that email address.');
            return self::FAILURE;
        }

        $previous = $user->role ?? 'member';
        $user->forceFill(['role' => $role, 'status' => 'Active'])->save();
        AdminAuditLog::create([
            'action' => 'role.assigned_by_console',
            'subject_type' => User::class,
            'subject_id' => (string) $user->id,
            'summary' => "Assigned {$role} role to {$user->email} from the console",
            'changes' => ['role' => ['from' => $previous, 'to' => $role]],
        ]);

        $this->info("{$user->email} is now {$role}.");
        return self::SUCCESS;
    }
}
