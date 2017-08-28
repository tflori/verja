<?php

use Verja\Gate;
use DependencyInjector\DI;

require_once 'vendor/autoload.php';

// we using tflori/dependency-injector here...
DI::set(
    'verja',                     // namne
    function() {                 // factory
        return new Gate();
    },
    false                        // share / singleton
);

// this is the controller
function controller() {
    /** @var Gate $verja */
    $verja = DI::get('verja');
}

controller();

// here comes the output to have a form and so on.. usually not inside the code...
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="chrome=1">
        <title>Verja Example</title>
        <link rel="stylesheet" href="docs/stylesheets/materialize.min.css">
        <link rel="stylesheet" href="docs/stylesheets/custom.css">
    </head>
    <body>
        <form method="post">

        </form>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="docs/javascripts/custom.js"></script>
        <script src="docs/javascripts/materialize.min.js"></script>
    </body>
</html>
