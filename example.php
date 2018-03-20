<?php

use Verja\Gate;
use Verja\Validator;
use DependencyInjector\DI;

require_once 'vendor/autoload.php';

// we using tflori/dependency-injector here...
DI::set(
    'verja',                     // namne
    function () {                // factory
        return new Gate();
    },
    false                        // share / singleton
);

// view model as simple array
$v = [];

// this is the controller
function controller()
{
    global $v;

    /** @var Gate $verja */
    $verja = DI::get('verja');
    $verja->addFields([
        'username'   => ['required', 'trim','strLen:3:20','!contains:fourtytwo'],
        'password'   => ['required', 'strLen:8'],
        'password_c' => ['required', 'equals:password'],
        'birth-date'  => ['dateTime', Validator::before(new DateTime('midnight -18 Years'))],
    ]);
    if (!empty($_POST) && $verja->validate($_POST)) {
        $v['success'] = true;
    } else {
        $v['success'] = false;
        $v['errors'] = $verja->getErrors();
    }
}

controller();

// view logic (usually in a template)
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
    <div class="container">
      <div class="row">
        <div class="col s6 offset-s3">
            <?php if ($v['success']) : ?>
              SUCCESS!
            <?php else : ?>
                <?php var_dump($v['errors']); ?>
            <?php endif; ?>
          <form method="post">
            <div class="input-field">
              <input type="text" name="username" id="username" value="<?= @$_POST['username'] ?>" />
              <label for="username">Username</label>
            </div>

            <div class="input-field">
              <input type="password" name="password" id="password" />
              <label for="password">Password</label>
            </div>

            <div class="input-field">
              <input type="password" name="password_c" id="password_c" />
              <label for="password_c">Repeat password</label>
            </div>

            <div class="input-field">
              <input type="text" class="datepicker" name="birth-date" id="birth-date"
                     value="<?= @$_POST['birth-date'] ?>" />
              <label for="birth-date">Birth date</label>
            </div>

            <button type="submit" class="waves-effect waves-light btn">register</button>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="docs/javascripts/custom.js"></script>
    <script src="docs/javascripts/materialize.min.js"></script>
    <script>
      $(document).ready(function() {
          $('.datepicker').datepicker({
              format: 'mm/dd/yyyy',
              selectMonth: true,
              yearRange: [1940, 2001],
          });
      });
    </script>
  </body>
</html>
