@extends('layouts.member')

@section('content')
@php
    $displayName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'Traderoom Member');
    $initials = collect(explode(' ', $displayName))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->join('');
    $currentPath = $user->current_path ?? 'Member Access';
    $verified = ! is_null($user->email_verified_at);
@endphp

<div class="ohc-profile-page">
    <section class="ohc-profile-hero">
        <div class="ohc-profile-hero__inner">
            <div class="ohc-profile-identity">
                                <div class="ohc-profile-avatar">
                    @if ($user->avatar_url)
                        <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="{{ $displayName }} profile photo">
                    @else
                        <span>{{ $initials ?: 'O' }}</span>
                    @endif
                </div>
                <div>
                    <p class="ohc-profile-kicker">Account Control</p>
                    <h1>Profile and Security</h1>
                    <p>Manage your Traderoom identity, login security, and protected member access.</p>
                </div>
            </div>

            <div class="ohc-profile-summary">
                <div>
                    <span>Name</span>
                    <strong>{{ $displayName }}</strong>
                </div>
                <div>
                    <span>Email status</span>
                    <strong class="{{ $verified ? 'is-positive' : 'is-warning' }}">{{ $verified ? 'Verified' : 'Pending verification' }}</strong>
                </div>
                <div>
                    <span>Access</span>
                    <strong>{{ $currentPath }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="ohc-profile-content">
        @if (session('status') === 'profile-updated' || session('status') === 'password-updated' || session('status') === 'verification-link-sent')
            <div class="ohc-profile-alert">
                @if (session('status') === 'profile-updated')
                    Profile information saved successfully.
                @elseif (session('status') === 'password-updated')
                    Password updated successfully.
                @else
                    A new verification link has been sent to your email address.
                @endif
            </div>
        @endif

        <div class="ohc-profile-grid">
            <div class="ohc-profile-stack">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="ohc-profile-stack">
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </section>
</div>
@endsection
