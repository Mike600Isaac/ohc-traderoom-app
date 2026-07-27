<section class="ohc-settings-card ohc-settings-card--danger">
    <div class="ohc-settings-card__header">
        <div>
            <p class="ohc-settings-eyebrow">Account risk</p>
            <h2>Close account</h2>
            <p>This permanently removes the member profile and account data connected to this login.</p>
        </div>
    </div>

    <button type="button" class="ohc-settings-button ohc-settings-button--danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Delete account
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="ohc-delete-modal">
            @csrf
            @method('delete')

            <p class="ohc-settings-eyebrow">Confirm deletion</p>
            <h2>Delete your Traderoom account?</h2>
            <p>Enter your password to confirm. This action cannot be undone.</p>

            <label class="ohc-settings-field" for="password">
                <span>Password</span>
                <input id="password" name="password" type="password" placeholder="Enter your password">
                @if ($errors->userDeletion->get('password'))
                    <small class="ohc-settings-error">{{ $errors->userDeletion->first('password') }}</small>
                @endif
            </label>

            <div class="ohc-delete-modal__actions">
                <button type="button" class="ohc-settings-button ohc-settings-button--ghost" x-on:click="$dispatch('close')">Cancel</button>
                <button type="submit" class="ohc-settings-button ohc-settings-button--danger">Delete account</button>
            </div>
        </form>
    </x-modal>
</section>
