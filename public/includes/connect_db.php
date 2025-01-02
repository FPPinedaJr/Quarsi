<?php
date_default_timezone_set("Asia/Manila");
$hostname = '77.37.35.51';
$defaultSchema = 'u273960544_quarsi';
$username = 'u273960544_fpjcm_quarsi';
$password = 'iLove69*';
$charset = 'utf8mb4';
$port = 3306 ;

$dsn = "mysql:host=$hostname;dbname=$defaultSchema;charset=$charset;port=$port";

$option =[PDO::ATTR_ERRMODE 			=> PDO::ERRMODE_EXCEPTION,
		 PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		 PDO::ATTR_EMULATE_PREPARES		=> false];

global $pdo;
$pdo= new PDO($dsn,$username,$password,$option);

