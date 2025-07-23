<?php

it('returns ok from /healthz', function () {
    $response = $this->get('/healthz');

    $response->assertOk()
             ->assertJsonPath('status', 'ok');
});
