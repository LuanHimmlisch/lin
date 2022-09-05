<?php

use LuanHimmlisch\Lin\Lin;

require_once __DIR__  . '/vendor/autoload.php';

$lin = Lin::make()
    ->setFunction(readline('Inserta la función a usar: '))
    ->setTolerance((float) readline('Inserta tolerancia: '));

$lin->execute();
