<?php
/**
 * SymfoPress server checker
 * Can be dipatch throught web request to
 * http://yoursite.ru/wp-content/plugins/symfopresss-lite/validator.php
 * Checks if server ready to install SymfoPress
 */
require_once './src/Netandreus/SymfopressBundle/ServerValidator.php';
$checker = new Netandreus\SymfopressBundle\ServerValidator();
print $checker->getPage();