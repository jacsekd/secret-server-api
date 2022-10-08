<?php

require_once('../model/Secret.php');
require_once('../db/Database.php');
require_once('./Responder.php');

if (!($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['secret']) && isset($_POST['expireAfterViews']) && isset($_POST['expireAfter']) && (is_int($_POST['expireAfterViews']) || ctype_digit($_POST['expireAfterViews'])) && (is_int($_POST['expireAfter']) || ctype_digit($_POST['expireAfter'])))) {
    http_response_code(405);
    return;
}

$database = new Database();
$db = $database->connect();
$secret = new Secret($db);

$secret->generateHash();

$now = strtotime(date("Y-m-d H:i:s.v"));
$secret->setCreatedAt(date("Y-m-d H:i:s", $now));
$secret->setExpiresAt(date("Y-m-d H:i:s", strtotime('+' . $_POST['expireAfter'] . ' minutes', $now)));
$secret->setRemainingViews($_POST['expireAfterViews']);
$secret->setSecretText($_POST['secret']);

if (!$secret->saveNew()) {
    http_response_code(405);
    return;
}

$responder = new Responder($_SERVER['HTTP_ACCEPT'] ?? '');
$responder->createResponse($secret);
header($responder->getResponseHeader());
echo $responder->getResponse();
