<?php
$server   = 'eu-cdbr-west-02.cleardb.net:3306';
$username = 'be8c5359d4d40e';
$password = '609c68a3';
$db       = 'heroku_360d2bca5a42a21';


/* Heroku config */
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host='.$server.';dbname=' . $db,
    'username' => $username,
    'password' => $password,
    'charset'  => 'utf8',
];



/* Local config */
/*return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=dblaba',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];*/




