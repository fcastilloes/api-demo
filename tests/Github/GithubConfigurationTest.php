<?php

namespace App\Tests\Github;

use App\Github\GithubConfiguration;
use PHPUnit\Framework\TestCase;

class GithubConfigurationTest extends TestCase
{
    public function testGetClientId()
    {
        $configuration = new GithubConfiguration(
            'clientId',
            'clientSecret',
            'apiUrl',
            'apiAccept'
        );

        $this->assertEquals('clientId', $configuration->getClientId());
    }

    public function testGetClientSecret()
    {
        $configuration = new GithubConfiguration(
            'clientId',
            'clientSecret',
            'apiUrl',
            'apiAccept'
        );

        $this->assertEquals('clientSecret', $configuration->getClientSecret());
    }

    public function testGetApiUrl()
    {
        $configuration = new GithubConfiguration(
            'clientId',
            'clientSecret',
            'apiUrl',
            'apiAccept'
        );

        $this->assertEquals('apiUrl', $configuration->getApiUrl());
    }

    public function testGetApiAccept()
    {
        $configuration = new GithubConfiguration(
            'clientId',
            'clientSecret',
            'apiUrl',
            'apiAccept'
        );

        $this->assertEquals('apiAccept', $configuration->getApiAccept());
    }
}
