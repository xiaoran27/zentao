<?php

$config->message->objectTypes['execution'] = $config->message->objectTypes['project'];
$config->message->available['webhook']['execution']        = $config->message->objectTypes['execution'];

// $config->message->available['mail']['project']        = $config->message->objectTypes['project'];
// $config->message->available['mail']['execution']        = $config->message->objectTypes['execution'];

// $config->message->available['message']['project']        = $config->message->objectTypes['project'];
// $config->message->available['message']['execution']        = $config->message->objectTypes['execution'];