<section class="ohc-settings-card">
    <div class="ohc-settings-card__header">
        <div>
            <p class="ohc-settings-eyebrow">Security</p>
            <h2>Password protection</h2>
            <p>Use a strong password that is not shared with any trading, email, or payment account.</p>
        </div>
        <span class="ohc-settings-badge ohc-settings-badge--dark">Protected</span>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="ohc-settings-form">
        @csrf
        @method('put')

        <label class="ohc-settings-field" for="update_password_current_password">
            <span>Current password</span>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
            @if ($errors->updatePassword->get('current_password'))
                <small class="ohc-settings-error">{{ $errors->updatePassword->first('current_password') }}</small>
            @endif
        </label>

        <div class="ohc-settings-fields ohc-settings-fields--two">
            <label class="ohc-settings-field" for="update_password_password">
                <span>New password</span>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password">
                @if ($errors->updatePassword->get('password'))
                    <small class="ohc-settings-error">{{ $errors->updatePassword->first('password') }}</small>
                @endif
            </label>

            <label class="ohc-settings-field" for="update_password_password_confirmation">
                <span>Confirm password</span>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                @if ($errors->updatePassword->get('password_confirmation'))
                    <small class="ohc-settings-error">{{ $errors->updatePassword->first('password_confirmation') }}</small>
                @endif
            </label>
        </div>

        <div class="ohc-password-rules">
            <span>12+ characters</span>
            <span>Uppercase and lowercase</span>
            <span>Number or symbol</span>
        </div>

        <div class="ohc-settings-actions">
            <button type="submit" class="ohc-settings-button">Update password</button>
        </div>
    </form>
</section>
