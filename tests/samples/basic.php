<?php
require __DIR__.'/../../vendor/autoload.php';

(new Tarsana\Application\Application)
    ->name('Test App')
    ->description('Just for test')
    ->version('1.0.1-beta')
    ->run();
