<?php

namespace App\Tests\Github;

use App\Github\GithubClient;
use App\Github\GithubConfiguration;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GithubClientTest extends TestCase
{

    public function testReadRepository()
    {
        $clientProphecy = $this->prophesize(Client::class);
        $configProphecy = $this->prophesize(GithubConfiguration::class);
        $responseProphecy = $this->prophesize(ResponseInterface::class);
        $streamProphecy = $this->prophesize(StreamInterface::class);

        $configProphecy->getApiUrl()->willReturn('url');
        $configProphecy->getApiAccept()->willReturn('accept');

        $streamProphecy->getContents()->willReturn('{"msg": "Hello World!"}');

        $responseProphecy->getBody()->willReturn($streamProphecy->reveal());

        $clientProphecy->send(Argument::any())->willReturn($responseProphecy->reveal());

        $githubClient = new GithubClient(
            $clientProphecy->reveal(),
            $configProphecy->reveal()
        );

        $this->assertEquals(
            ['msg' => 'Hello World!'],
            $githubClient->readRepository('token', 'owner', 'repo')
        );
    }
}
