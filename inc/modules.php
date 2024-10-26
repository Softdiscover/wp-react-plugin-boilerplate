<?php

declare(strict_types=1);

use Me\Plugin\Demo\DemoModule;
use Me\Plugin\ApiFetcher\AfModule;

return function (string $rootDir, string $mainFile): iterable {
    return [
       new DemoModule()
    ];
};
