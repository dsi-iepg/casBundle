<?php

namespace Iepg\Bundle\Cas\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\HttpFoundation\RequestStack;

class CasAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly array $casConfig,
        private readonly UrlGeneratorInterface $router,
        private RequestStack $requestStack
    ) {}
    

    public function supports(Request $request): ?bool
    {
        return ($request->getMethod() === 'GET' &&
            $request->attributes->get('_route') === 'cas_login'
        );
    }

    public function authenticate(Request $request): Passport
    {
        $fullBaseUrl = $this->getUrlDynamique();

   // Initialisez phpCAS
        \phpCAS::client(
            CAS_VERSION_2_0,
            $this->casConfig['cas_host'],
            (int) $this->casConfig['cas_port'],
            $this->casConfig['cas_path'],
            $fullBaseUrl,
        );

        if ($this->casConfig['cas_ca'] === false) {
            \phpCAS::setNoCasServerValidation();
        } elseif (!empty($this->casConfig['cas_ca_path'])) {
            \phpCAS::setCasServerCACert($this->casConfig['cas_ca_path']);
        }

        \phpCAS::forceAuthentication();
        $username = \phpCAS::getUser();

        return new SelfValidatingPassport(
            new UserBadge($username)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $target = (null == $this->casConfig['cas_dispatcher_name'])? 
                'cas_dispatcher' : $this->casConfig['cas_dispatcher_name'];
        
        return new RedirectResponse(
            $this->router->generate($target)
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (null != $_SESSION['phpCAS']['user']) {

            return new RedirectResponse(
                $this->router->generate('cas_unknow-home')
            );
        };

        $target = (null == $this->cas_dispatcher_name)? 'cas_dispatcher' : $this->cas_dispatcher_name;
        
        return new RedirectResponse(
            $this->router->generate($target)
        );
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('cas_login')
        );
    }

    private function getUrlDynamique(): string
    {
        // Récupère la requête courante (peut être null en CLI)
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return 'http://localhost'; // Valeur de repli pour la CLI
        }

        // Cas 1 : Récupérer "https://monsite.com" (Schéma + Hôte)
        $domain = $request->getSchemeAndHttpHost();

        // Cas 2 : Récupérer "https://monsite.com/mon-app" (Si installé dans un sous-dossier)
        $fullBaseUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl();

        return $fullBaseUrl;
    }
}
