<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\KajabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KajabiBridgeController extends Controller
{
    public function login(Request $request, \App\Services\KajabiService $kajabi)
    {
        $email = $request->query('email');

        // 1. If the URL didn't provide an email, we can't query anything.
        if (!$email || $email == '{{ email }}') {
            return redirect('/login')->with('error', 'No identity provided by Kajabi.');
        }

        // 2. THE TECHNICAL QUERY: Ask the Kajabi API if this email is valid
        $memberData = $kajabi->getKajabiMember($email);

        if (!$memberData) {
            // If the API says "No," we block them.
            return redirect('/login')->with('error', 'Kajabi API could not verify this member.');
        }

        // 3. SYNC & LOGIN: If the API said "Yes," we update our MySQL and log them in.
        $user = \App\Models\User::updateOrCreate(
            ['email' => $email],
            [
                'kajabi_user_id' => $memberData['id'],
                'first_name'     => $memberData['first_name'] ?? 'Member',
                'last_name'      => $memberData['last_name'] ?? '',
                'password'       => \Illuminate\Support\Facades\Hash::make('12345678'), // Placeholder
                'current_path'   => 'Free', 
            ]
        );

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('dashboard');
    }
}