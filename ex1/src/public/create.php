<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Donor.php";
require_once __DIR__ . "/../repositories/DonorRepository.php";
require_once __DIR__ . "/../Controllers/BaseController.php";
require_once __DIR__ . "/../Controllers/DonorController.php";

$controller = new \App\Controllers\DonorController();
$controller->create();
