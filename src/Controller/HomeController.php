<?php

namespace App\Controller;

use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Google;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $github_provider;
    private $google_provider;

    public function __construct()
    {
        $this->github_provider = new Github([
            'clientId' => $_ENV['GITHUB_ID'],
            'clientSecret' => $_ENV['GITHUB_SECRET'],
            'redirectUri' => $_ENV['GITHUB_CALLBACK']
        ]);

        $this->google_provider = new Google([
            'clientId' => $_ENV['GG_ID'],
            'clientSecret' => $_ENV['GG_SECRET'],
            'redirectUri' => $_ENV['GG_CALLBACK'],
            'accessType' => $_ENV['GG_accessType']
        ]);
    }

    #[Route('/', name: "home")]
    public function home(): Response
    {
        return $this->render("home.html.twig");
    }

    #[Route('/github_login', name: "github_login")]
    public function gitHubLogin(): Response
    {
        $options = [
            'scope' => ['user', 'user:email', 'repo'] // array or string; at least 'user:email' is required
        ];
        $helper_url = $this->github_provider->getAuthorizationUrl($options);
        return $this->redirect($helper_url);
    }

    #[Route('/github_callback', name: "github_callback")]
    public function gitHubCallback(): Response
    {
        $token = $this->github_provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        dd($token);
        $user = $this->github_provider->getResourceOwner($token);
        dd($user);
        try {
            $user = $this->github_provider->getResourceOwner($token);
            dd($user);

            $user = $user->Toarray();
            $nom = $user['login'];
            $picture = $user['avatar_url'];
            return  $this->render('show.html.twig', [
                'name' => $nom,
                'picture' => $picture,
            ]);
        } catch (\throwable $th) {
            return $th->getMessage();
        }
    }
    // login via Google
    #[Route('/google_login', name: "google_login")]
    public function googleLogin(): Response
    {
        $options = [
            'scope' => ['email profile'] // array or string; at least 'user:email' is required
        ];
        $helper_url = $this->google_provider->getAuthorizationUrl($options);
        return $this->redirect($helper_url);
    }
    // callback
    #[Route('/google_callback', name: "google_callback")]
    public function googleCallback(): Response
    {
        $token = $this->google_provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        $user = $this->google_provider->getResourceOwner($token);

        try {
            $user = $this->google_provider->getResourceOwner($token);
            dd($user);

            $user = $user->Toarray();
            $nom = $user['login'];
            $picture = $user['avatar_url'];
            return  $this->render('show.html.twig', [
                'name' => $nom,
                'picture' => $picture,
            ]);
        } catch (\throwable $th) {
            return $th->getMessage();
        }
    }
}