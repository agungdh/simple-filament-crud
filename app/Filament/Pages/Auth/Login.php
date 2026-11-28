<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Support\Arr;
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

        $imageIds = config('login.wallpaper.ids', []);

        if (blank($imageIds)) {
            return null;
        }

        $provider = rtrim((string) config('login.wallpaper.provider'), '/');
        $imageId = Arr::random($imageIds);

        $url = "{$provider}/id/{$imageId}/1920/1080";
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
