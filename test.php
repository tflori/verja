<?php

use Verja\Gate;

require_once 'vendor/autoload.php';

$gate = new Gate();
$gate->accept('test', ['required', 'numeric:,']);

if ($gate->validate(['test' => '1.000,5E-3'])) {
    $data = $gate->getData();
    var_dump(isset($data['test']), $data);
} else {
    var_dump($gate->getErrors()['test']);
}
