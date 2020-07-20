<?php declare(strict_types = 1);

namespace GithubRepositoryConfigurator;

use GithubRepositoryConfigurator\Repository\ConfigureRepositoryCommand;
use Symfony\Component\Console\Application;

final class ConsoleApp
{
    public static function run(string $secretKey): void
    {
        $configureRepository = new ConfigureRepositoryCommand($secretKey);

        $app = new Application();

        $app->add($configureRepository);

        $app->run();
    }
}
