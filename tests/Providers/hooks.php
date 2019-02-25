<?php

use Ink\Hooks\ActionManager;
use Ink\Contracts\Hooks\FilterManager;

if ($actionManager instanceof ActionManager && $filterManager instanceof FilterManager) {
    echo 'Hooks loaded !';
}