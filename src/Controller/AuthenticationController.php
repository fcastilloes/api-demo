<?php

namespace App\Controller;

use App\Github\GithubClient;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController extends FOSRestController
{
    /**
     * @Rest\Post("/authorize_code", name="authorize_code")
     *
     * @param Request $request
     * @param GithubClient $client
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request, GithubClient $client)
    {
        $response = $client->authorize($request->request->get('code'));

        $view = $this->view($response, 200);

        return $this->handleView($view);
    }
}
