<?php

$router->prefix('foo')->group(
    function ($router) {
        $router->prefix('bar')
            ->group(
                function ($router) {
                    $router->get('/baz', 'StubController@handler');
                    $router->get('{baz}', 'StubController@handler');
                }
            );
    }
);

