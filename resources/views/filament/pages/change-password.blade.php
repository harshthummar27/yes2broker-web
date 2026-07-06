<x-filament-panels::page>
    <div class="max-w-xl">
        @if($forgotCurrentPassword)
            <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-600 dark:bg-amber-950/40 dark:text-amber-100">
                <p class="font-semibold">Forgot current password</p>
                <p class="mt-1">
                    Enter your new password below. An approval email will be sent to
                    <strong>{{ mask_email(config('auth.password_change.approval_email')) }}</strong>.
                    Your password will update only after approval.
                </p>
            </div>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Submit a password change request. An approval email with <strong>Yes</strong> and <strong>No</strong> options will be sent to
                <strong>{{ mask_email(config('auth.password_change.approval_email')) }}</strong>. Your password updates only if approved.
            </p>
        @endif

        <form wire:submit="submit" class="space-y-6">
            {{ $this->form }}

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <x-filament::button type="submit">
                    {{ $forgotCurrentPassword ? 'Request Password Reset' : 'Request Password Change' }}
                </x-filament::button>

                @if($forgotCurrentPassword)
                    <button
                        type="button"
                        wire:click="useCurrentPassword"
                        class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                        I remember my current password
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="enableForgotCurrentPassword"
                        class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                        Forgot current password?
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-filament-panels::page>
