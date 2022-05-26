<?php

require dirname(__DIR__) . '/vendor/autoload.php';

passthru('./artisan migrate:fresh -q');
passthru('./artisan db:seed');
