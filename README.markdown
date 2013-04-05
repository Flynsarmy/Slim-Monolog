# Monolog Logging for Slim Framework

This repository adds support for logging to [Monolog](https://github.com/Seldaek/monolog) to the [Slim Framework](http://www.slimframework.com/).

# Installation

MonologWriter takes an array of handlers, an array of processors and a logger name.

```php
$logger = new \Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => array(
        new \Monolog\Handler\StreamHandler('./logs/'.date('Y-m-d').'.log'),
    ),
));

$app = new \Slim\Slim(array(
    'log.writer' => $logger,
));
```

This example assumes you are autoloading dependencies using [Composer](http://getcomposer.org/). If you are not
using Composer, you must manually `require` the log writer class before instantiating it.

# License

The Slim-Monolog is released under the MIT public license.
