<?php
date_default_timezone_set("Asia/Manila");
$charset = 'utf8mb4';

// ----- PRODUCTION DATABASE ----- //
$hostname = '77.37.35.51';
$port = 3306 ;
$username = 'u273960544_fpjcm_quarsi';
$password = '12345siX*';
$defaultSchema = 'u273960544_quarsi';

// ----- DEVELOPMENT DATABASE ----- //
$hostname = '77.37.35.51';
$port = 3306 ;
$username = 'u273960544_tester';
$password = 'TesTing123*';
$defaultSchema = 'u273960544_prototype';



$dsn = "mysql:host=$hostname;dbname=$defaultSchema;charset=$charset;port=$port";

$option =[PDO::ATTR_ERRMODE 			=> PDO::ERRMODE_EXCEPTION,
		 PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		 PDO::ATTR_EMULATE_PREPARES		=> false];

global $pdo;
$pdo= new PDO($dsn,$username,$password,$option);

