<?php

use Verja\Gate;

require_once 'vendor/autoload.php';

$gate = new Gate();
$gate->accept('test', ['required', 'integer']);

if ($gate->validate(['test' => ''])) {
    $data = $gate->getData();
    var_dump(isset($data['test']), $data);
} else {
    var_dump($gate->getErrors()['test']);
}
