<?php

namespace App\Controller;

use App\Github\GithubClient;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;

class RepositoryController extends FOSRestController
{
    /**
     * @Annotations\Get(
     *     path="/repositories/{owner}/{repo}"
     * )
     *
     * @param GithubClient $client
     * @param Request $request
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getRepositoryAction(
        GithubClient $client,
        Request $request,
        LoggerInterface $logger,
        CacheInterface $cache
    ) {
        $owner = $request->get('owner');
        $repo = $request->get('repo');
        $cacheKey = "repo;$owner;$repo";

        if ($cache->has($cacheKey)) {
            $view = $this->view($cache->get($cacheKey), 200, [
                'X-From-Cache' => 'true',
            ]);
            return $this->handleView($view);
        }

        try {
            $response = $client->readRepository(
                $request->headers->get('Authorization'),
                $owner,
                $repo
            );

            $cache->set($cacheKey, $response, 300);

            $view = $this->view($response, 200);

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
     * @Annotations\Get(
     *     path="/repositories/{owner}/{repo}/issues"
     * )
     *
     * @param GithubClient $client
     * @param Request $request
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRepositoryIssuesAction(
        GithubClient $client,
        Request $request,
        LoggerInterface $logger
    ) {
        try {
            $response = $client->listRepositoryIssues(
                $request->headers->get('Authorization'),
                $request->get('owner'),
                $request->get('repo')
            );

            $view = $this->view($response, 200);
        } catch (GuzzleException $e) {
            $logger->error($e->getMessage());
            $view = $this->view(null, 500);
        }

        return $this->handleView($view);
    }
}
