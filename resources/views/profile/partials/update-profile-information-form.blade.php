<section class="ohc-settings-card">
    <div class="ohc-settings-card__header">
        <div>
            <p class="ohc-settings-eyebrow">Profile</p>
            <h2>Personal information</h2>
            <p>Keep your account name, photo, and contact email accurate for course access, billing, and member support.</p>
        </div>
        <span class="ohc-settings-badge">Member record</span>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="ohc-settings-form" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="ohc-avatar-upload">
            <div class="ohc-avatar-upload__preview">
                @if ($user->avatar_url)
                    <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'Traderoom member' }} profile photo">
                @else
                    <span>{{ strtoupper(substr($user->first_name ?? $user->name ?? 'O', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}</span>
                @endif
            </div>
            <div class="ohc-avatar-upload__copy">
                <label class="ohc-avatar-upload__button" for="avatar">Upload profile image</label>
                <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp">
                <p>JPG, PNG, or WEBP. Maximum size 2MB.</p>
                @error('avatar')
                    <small class="ohc-settings-error">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="ohc-settings-fields ohc-settings-fields--two">
            <label class="ohc-settings-field" for="first_name">
                <span>First name</span>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required autofocus autocomplete="given-name">
                @error('first_name')
                    <small class="ohc-settings-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="ohc-settings-field" for="last_name">
                <span>Last name</span>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required autocomplete="family-name">
                @error('last_name')
                    <small class="ohc-settings-error">{{ $message }}</small>
                @enderror
            </label>
        </div>

        <label class="ohc-settings-field" for="email">
            <span>Email address</span>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <small class="ohc-settings-error">{{ $message }}</small>
            @enderror
        </label>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="ohc-settings-note ohc-settings-note--warning">
                <span>Your email is not verified yet.</span>
                <button form="send-verification" type="submit">Resend verification email</button>
            </div>
        @endif

        <div class="ohc-settings-actions">
            <button type="submit" class="ohc-settings-button">Save profile</button>
        </div>
    </form>
</section>