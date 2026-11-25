<?php

test('the root redirects to the admin panel', function () {
    $this->get('/')->assertRedirect('/admin');
});
