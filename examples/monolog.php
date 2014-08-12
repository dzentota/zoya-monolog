<?php

require __DIR__ . '/../vendor/autoload.php';


$stream = new \Monolog\Handler\StreamHandler('php://stderr', \Monolog\Logger::DEBUG);
$formatter = new \Zoya\Monolog\Formatter\ColoredConsoleFormatter();
$stream->setFormatter($formatter);
$logger = new \Monolog\Logger('CLIENT');
$logger->pushHandler($stream);

$logger->debug('debug message');
$logger->info('info message');
$logger->notice('notice message');
$logger->warning('warning message');
$logger->error('error message');
$logger->critical('critical message');
$logger->alert('alert message');
$logger->emergency('emergency message');
