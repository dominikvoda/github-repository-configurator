<?php declare(strict_types = 1);

use GithubRepositoryConfigurator\ConsoleApp;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;

require_once __DIR__ . '/vendor/autoload.php';

$config = Neon::decode(FileSystem::read(__DIR__ . '/config.neon'));

ConsoleApp::run($config['secretKey']);
