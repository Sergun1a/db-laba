<?php
$server   = 'eu-cdbr-west-03.cleardb.net:3306';
$username = 'b20409eee3af56';
$password = 'e9bda834';
$db       = 'heroku_e130fe6d416e3df';

/* Heroku config */
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host='.$server.';dbname=' . $db,
    'username' => $username,
    'password' => $password,
    'charset'  => 'utf8',
];



/* Local config
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=dblaba',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
*/



