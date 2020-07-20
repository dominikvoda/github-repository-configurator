<?php declare(strict_types = 1);

namespace GithubRepositoryConfigurator;

use GithubRepositoryConfigurator\Repository\RepositoryName;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use function array_map;

final class GithubClient
{
    private const METHOD_PATCH = 'PATCH';
    private const METHOD_PUT = 'PUT';
    private const METHOD_GET = 'GET';

    /**
     * @var string
     */
    private $secretKey;


    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }


    public function updateRepositorySettings(
        RepositoryName $repositoryName,
        bool $allowSquashMerge,
        bool $allowMergeCommit,
        bool $allowRebaseMerge,
        bool $deleteBrandOnMerge
    ): void {
        $path = '/repos/' . $repositoryName->getFullName();
        $this->getResponse(
            $path,
            self::METHOD_PATCH,
            [
                'allow_squash_merge' => $allowSquashMerge,
                'allow_merge_commit' => $allowMergeCommit,
                'allow_rebase_merge' => $allowRebaseMerge,
                'delete_branch_on_merge' => $deleteBrandOnMerge,
            ]
        );
    }


    public function updateRepositoryBranchProtection(
        RepositoryName $repositoryName,
        string $branchName = 'master'
    ): void {
        $path = '/repos/' . $repositoryName->getFullName() . '/branches/' . $branchName . '/protection';

        $this->getResponse(
            $path,
            self::METHOD_PUT,
            [
                'enforce_admins' => null,
                'required_status_checks' => ['strict' => true, 'contexts' => []],
                'required_pull_request_reviews' => ['required_approving_review_count' => 2],
                'restrictions' => null,
            ]
        );
    }


    /**
     * @return RepositoryName[]
     */
    public function searchRepositories(string $query): array
    {
        $path = '/search/repositories?q=' . $query;

        $response = $this->getResponse($path);
        $parsedResponse = $this->parseJsonResponse($response);

        return array_map(
            static function (array $repository): RepositoryName {
                return new RepositoryName($repository['full_name']);
            },
            $parsedResponse['items']
        );
    }


    private function getResponse(
        string $path,
        string $method = self::METHOD_GET,
        array $requestBody = []
    ): ResponseInterface {
        $client = $this->createClient();

        $requestOptions = [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/vnd.github.luke-cage-preview+json',
            ],
        ];

        if ($method !== self::METHOD_GET) {
            $requestOptions[RequestOptions::JSON] = $requestBody;
        }

        return $client->request($method, $this->createUri($path), $requestOptions);
    }


    private function createUri(string $path): Uri
    {
        return new Uri('https://api.github.com' . $path);
    }


    private function createClient(): ClientInterface
    {
        return new Client();
    }


    private function parseJsonResponse(ResponseInterface $response): array
    {
        return Json::decode((string)$response->getBody(), Json::FORCE_ARRAY);
    }
}
