<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\PasswordChangeRequestService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\Rules\Password;
use Throwable;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static string $view = 'filament.pages.change-password';

    protected static ?string $title = 'Change Password';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public bool $forgotCurrentPassword = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->revealable()
                    ->visible(fn (): bool => ! $this->forgotCurrentPassword)
                    ->required(fn (): bool => ! $this->forgotCurrentPassword)
                    ->rule(fn (): array => $this->forgotCurrentPassword ? [] : ['current_password']),
                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->confirmed()
                    ->rule(Password::defaults()),
                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function enableForgotCurrentPassword(): void
    {
        $this->forgotCurrentPassword = true;
        $this->data['current_password'] = null;
    }

    public function useCurrentPassword(): void
    {
        $this->forgotCurrentPassword = false;
        $this->data['current_password'] = null;
    }

    public function submit(PasswordChangeRequestService $service): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        try {
            $service->createRequest($user, $data['password'], $this->forgotCurrentPassword);
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Could not send approval email')
                ->body('Please try again later or contact support.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Approval email sent')
            ->body($this->forgotCurrentPassword
                ? 'Because you forgot your current password, an approval email was sent to '.mask_email(config('auth.password_change.approval_email')).'. Your password will change only after approval.'
                : 'An email was sent to '.mask_email(config('auth.password_change.approval_email')).'. Your password will change only after approval.')
            ->success()
            ->send();

        $this->forgotCurrentPassword = false;
        $this->form->fill();
    }
}
