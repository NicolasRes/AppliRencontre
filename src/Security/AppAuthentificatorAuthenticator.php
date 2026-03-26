<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Entity\Utilisateur;

class AppAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', ''); // Récupère le champ 'name="email"' du formulaire
        $password = $request->request->get('password', ''); // Récupère le champ 'name="password"'

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }
// src/Security/AppAuthentificatorAuthenticator.php

public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    $user = $token->getUser();

    // Redirection en fonction du statut
    if ($user instanceof Utilisateur) {

        //Si c'est un modérateur, on l'envoie directement sur les signalements
        if ($user->isModo()) {
            return new RedirectResponse($this->urlGenerator->generate('app_moderateur_signalements'));
        }
    
        // En attente de validation
        if ($user->isPending()) {
            return new RedirectResponse($this->urlGenerator->generate('home'));
        }
        // Rejeté : doit corriger ses infos
        elseif ($user->isRejected()) {
            return new RedirectResponse($this->urlGenerator->generate('app_register'));
        }
        // Redirection vers la homepage si validé
        elseif ($user->isApproved()) {
            return new RedirectResponse($this->urlGenerator->generate('app_home_page'));
        }
    }

    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->urlGenerator->generate('app_home_page'));
}

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
