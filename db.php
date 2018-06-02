<?php
$DbConfig = parse_ini_file('config.ini');
$db = new PDO( 'mysql:host=localhost;dbname=SpaceInvaders', $DbConfig[ 'Uid'], $DbConfig[ 'Password']);
