<?php

namespace phpList\plugin\Common;

use Mouf\Picotainer\Picotainer;
use Psr\Container\ContainerInterface as Psr11Container;

/**
 * Convenience class to always include the Common Plugin dependencies in picotainer.
 */
class Container extends Picotainer
{
    public function __construct(array $depends, Psr11Container $delegateLookupContainer = null)
    {
        $localDepends = include __DIR__ . '/depends.php';
        parent::__construct($depends + $localDepends, $delegateLookupContainer);
    }
}
