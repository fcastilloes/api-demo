<?php

namespace App\Controller;

use App\Github\GithubClient;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Nexy\Slack\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class WebhookController extends FOSRestController
{
    /**
     * @Rest\Post("/receive_github_webhook")
     * @param Request $request
     * @param Client $slack
     * @param \Predis\Client $predis
     * @param \GuzzleHttp\Client $guzzle
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Http\Client\Exception
     * @throws GuzzleException
     */
    public function receiveGithubWebhook(
        Request $request,
        Client $slack,
        \Predis\Client $predis,
        \GuzzleHttp\Client $guzzle,
        \Swift_Mailer $swift
    ) {
        $body = json_decode($request->getContent(), true);
        if ($body['action'] === 'opened') {
            $message = $slack->createMessage()
                ->setText($body['issue']['title']);
            $slack->sendMessage($message);

            if ($predis->get($body['repository']['full_name'])) {
                $request = new \GuzzleHttp\Psr7\Request(
                    'POST',
                    $predis->get($body['repository']['full_name']),
                    ['Content-Type' => 'application/json'],
                    json_encode($body['issue'])
                );

                $guzzle->send($request);
            }

            $message = (new \Swift_Message('New Issue'))
                ->setFrom('send@example.com')
                ->setTo('contact@example.com')
                ->setBody($body['issue']['title'], 'text/plain');

            $swift->send($message);

            return $this->handleView($this->view('OK', 200));
        }

        return $this->handleView($this->view(null, 200));
    }


    /**
     * @Rest\Get("/repositories/{owner}/{repo}/webhook")
     *
     * @param GithubClient $client
     * @param Request $request
     * @param \Predis\Client $predis
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readWebhook(
        GithubClient $client,
        Request $request,
        \Predis\Client $predis,
        LoggerInterface $logger
    ) {
        try {
            $client->readRepository(
                $request->headers->get('Authorization'),
                $request->get('owner'),
                $request->get('repo')
            );

            $repository = "{$request->get('owner')}/{$request->get('repo')}";
            $url = $predis->get($repository) ?? '';

            $view = $this->view([
                'repository' => $repository,
                'url' => $url,
            ], 200);

        } catch (GuzzleException $e) {
            if ($e instanceof RequestException && $e->getResponse()->getStatusCode() === 404) {
                $view = $this->view(null, 404);
            } else {
                $logger->error($e->getMessage());
                $view = $this->view(null, 500);
            }
        }

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/repositories/{owner}/{repo}/webhook")
     * @Rest\RequestParam(name="url", description="test")
     *
     * @param GithubClient $client
     * @param Request $request
     * @param \Predis\Client $predis
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setWebhook(
        GithubClient $client,
        Request $request,
        \Predis\Client $predis,
        LoggerInterface $logger
    ) {
        try {
            $client->readRepository(
                $request->headers->get('Authorization'),
                $request->get('owner'),
                $request->get('repo')
            );

            $repository = "{$request->get('owner')}/{$request->get('repo')}";
            $predis->set($repository, $request->get('url'));

            $view = $this->view([
                'repository' => $repository,
                'url' => $request->request->get('url'),
            ], 200);

        } catch (GuzzleException $e) {
            if ($e instanceof RequestException && $e->getResponse()->getStatusCode() === 404) {
                $view = $this->view(null, 404);
            } else {
                $logger->error($e->getMessage());
                $view = $this->view(null, 500);
            }
        }

        return $this->handleView($view);
    }


}
