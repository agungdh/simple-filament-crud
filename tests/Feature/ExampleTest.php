<?php

use Filament\Facades\Filament;

test('the root redirects to the admin panel', function () {
    $this->get('/')->assertRedirect('/admin');
});

test('global search is disabled', function () {
    Filament::setCurrentPanel('admin');

    expect(Filament::getGlobalSearchProvider())->toBeNull();
});
