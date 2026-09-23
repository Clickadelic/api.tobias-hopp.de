<?php

it('returns a JSON 401 for unauthenticated API requests without an Accept header', function () {
    $this->get('/api/user')
        ->assertUnauthorized()
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});
