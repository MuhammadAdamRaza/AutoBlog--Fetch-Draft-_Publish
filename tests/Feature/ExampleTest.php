<?php

it('renders the public blog index', function () {
    $this->get(route('blog.index'))
        ->assertOk();
});
