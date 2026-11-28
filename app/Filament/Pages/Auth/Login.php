<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    /**
     * @return array<string, string>
     */
    #[Unexposed]
    public function getExtraBodyAttributes(): array
    {
        $wallpaperUrl = $this->getWallpaperUrl();

        if ($wallpaperUrl === null) {
            return [];
        }

        return [
            'class' => 'fi-login-wallpaper',
            'style' => "--login-wallpaper-url: url('{$wallpaperUrl}');",
        ];
    }

    private function getWallpaperUrl(): ?string
    {
        if (! config('login.wallpaper.enabled')) {
            return null;
        }

        $configuredUrl = config('login.wallpaper.url');

        if (filled($configuredUrl)) {
            return filter_var($configuredUrl, FILTER_VALIDATE_URL) ?: null;
        }

        $provider = rtrim((string) config('login.wallpaper.provider'), '/');
        $seed = Str::random(16);

        $url = "{$provider}/seed/{$seed}/1920/1080";
        $blur = (int) config('login.wallpaper.blur');

        $url = $blur > 0 ? "{$url}?blur={$blur}" : $url;

        return filter_var($url, FILTER_VALIDATE_URL) ?: null;
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }
}
