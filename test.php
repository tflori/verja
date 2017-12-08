<?php

use Verja\Gate;

require_once 'vendor/autoload.php';

$gate = new Gate();
$gate->accept('test', '!truthful');

if ($gate->validate(['test' => '1'])) {
    var_dump($gate->getData());
} else {
    var_dump($gate->getErrors());
}
