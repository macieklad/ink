<?php

$router->prefix('foo')
    ->group(
        function ($router) {
            $router->get('/bar', 'StubController@handler');
            $router->get('{baz}', 'StubController@handler');
        }
    );
