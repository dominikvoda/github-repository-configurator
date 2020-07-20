<?php declare(strict_types = 1);

namespace GithubRepositoryConfigurator\Repository;

use function assert;
use function explode;

final class RepositoryName
{
    private $owner;

    private $name;


    public function __construct(string $repositoryName)
    {
        $parts = explode('/', $repositoryName);

        assert(count($parts) === 2);

        $this->owner = $parts[0];
        $this->name = $parts[1];
    }


    /**
     * @return mixed|string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }


    /**
     * @return mixed|string
     */
    public function getName(): string
    {
        return $this->name;
    }


    public function getFullName(): string
    {
        return $this->getOwner() . '/' . $this->getName();
    }
}
