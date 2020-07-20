<?php declare(strict_types = 1);

namespace GithubRepositoryConfigurator\Repository;

use GithubRepositoryConfigurator\GithubClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConfigureRepositoryCommand extends Command
{
    /**
     * @var string
     */
    private $secretKey;


    public function __construct(string $secretKey)
    {
        parent::__construct();
        $this->secretKey = $secretKey;
    }


    public function configure(): void
    {
        $this->setName('github:repository:configure');
    }


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $githubClient = new GithubClient($this->secretKey);

        $repositories = $githubClient->searchRepositories(
            'org:BrandEmbassy language:php is:private channel-integration in:name'
        );

        foreach ($repositories as $repositoryName) {
            $output->writeln('Updating ' . $repositoryName->getFullName());

            $githubClient->updateRepositorySettings($repositoryName, true, false, false, true);
            $githubClient->updateRepositoryBranchProtection($repositoryName);

            $output->writeln('Done');
        }

        return 0;
    }
}
