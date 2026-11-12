<?php

it('redirects the root url to the admin login', function () {
    $this->get('/')->assertRedirect('/admin/login');
});
