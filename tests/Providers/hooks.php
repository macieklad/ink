<?php

use Ink\Hooks\ActionManager;
use Ink\Contracts\Hooks\FilterManager;

$actionManagerLoaded = $actionManager instanceof ActionManager;
$filterManagerLoaded = $filterManager instanceof FilterManager;

if ($actionManagerLoaded && $filterManagerLoaded) {
    echo 'Hooks loaded !';
}