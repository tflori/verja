<?php

use Verja\Gate;

require_once 'vendor/autoload.php';

$gate = new Gate();
$gate->accept('test', ['required', 'ipAddress:v6:public,unreserved']);

if ($gate->validate(['test' => $_SERVER['argv'][1]])) {
    $data = $gate->getData();
    var_dump(isset($data['test']), $data);
} else {
    var_dump($gate->getErrors()['test']);
}
