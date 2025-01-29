<?php

require_once __DIR__ . '/../app.php';
require_once __DIR__ . '/../routes/routes.php';
require_once __DIR__ . '/../Controllers/ApiController.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Donor.php';
require_once __DIR__ . '/../repositories/DonorRepository.php';

$app = new \App\Core\App();
$app->run();
