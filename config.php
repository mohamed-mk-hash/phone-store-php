<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Chargily\ChargilyPay\Auth\Credentials;
use Chargily\ChargilyPay\ChargilyPay;


$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$credentials = new Credentials([
    "mode" => $_ENV['MODE'],
    "public" => $_ENV['CHARGILY_PUBLIC'],
    "secret" => $_ENV['CHARGILY_SECRET'],
]);

$chargily_pay = new ChargilyPay($credentials);
?>
