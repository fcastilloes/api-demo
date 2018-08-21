<?php

namespace App\Github;

class GithubConfiguration
{
    /**
     * @var string
     */
    private $clientId = '';

    /**
     * @var string
     */
    private $clientSecret = '';

    /**
     * @var string
     */
    private $apiUrl = '';

    /**
     * @var string
     */
    private $apiAccept = '';

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $apiUrl
     * @param string $apiAccept
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $apiUrl,
        string $apiAccept
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->apiUrl = $apiUrl;
        $this->apiAccept = $apiAccept;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getApiAccept(): string
    {
        return $this->apiAccept;
    }
}
