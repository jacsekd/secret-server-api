<?php

require_once('../model/Secret.php');
require_once('../db/Database.php');
require_once('./Responder.php');

$database = new Database();
$db = $database->connect();

$secret = new Secret($db);

$secret->setHash($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hash']) ? $_GET['hash'] : http_response_code(404));

if (!$secret->readOne()) {
    http_response_code(404);
    return;
}

$responder = new Responder($_SERVER['HTTP_ACCEPT'] ?? '');
$responder->createResponse($secret);
header($responder->getResponseHeader());
echo $responder->getResponse();
