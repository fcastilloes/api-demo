<?php

namespace App\Github;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class GithubClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var GithubConfiguration
     */
    private $config;

    /**
     * @param string $method
     * @param string $path
     * @param string $token
     * @return RequestInterface
     */
    protected function prepareRequest(
        string $method,
        string $path,
        string $token
    ): RequestInterface {
        $request = new Request($method, $this->config->getApiUrl() . $path);

        return $request
            ->withAddedHeader('Accept', $this->config->getApiAccept())
            ->withAddedHeader('Authorization', "$token");
    }

    /**
     * @param Client $client
     * @param GithubConfiguration $config
     */
    public function __construct(Client $client, GithubConfiguration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $code
     * @return mixed
     * @throws GuzzleException
     */
    public function authorize(string $code)
    {
        $request = new Request(
            'POST',
            new Uri('https://github.com/login/oauth/access_token'),
            [],
            sprintf(
                'client_id=%s&client_secret=%s&code=%s',
                $this->config->getClientId(),
                $this->config->getClientSecret(),
                $code
            )
        );

        $response = $this->client->send(
            $request->withHeader('Accept', 'application/json')
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $token
     * @return mixed
     * @throws GuzzleException
     */
    public function readAuthenticatedUser(string $token)
    {
        $request = $this->prepareRequest('GET', '/user', $token);
        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $token
     * @param string $owner
     * @param string $repo
     * @return mixed
     * @throws GuzzleException
     */
    public function readRepository(string $token, string $owner, string $repo)
    {
        $request = $this->prepareRequest('GET', "/repos/$owner/$repo", $token);
        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $token
     * @param string $owner
     * @param string $repo
     * @return mixed
     * @throws GuzzleException
     */
    public function listRepositoryIssues(string $token, string $owner, string $repo)
    {
        $request = $this->prepareRequest('GET', "/repos/$owner/$repo/issues", $token);
        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }
}
