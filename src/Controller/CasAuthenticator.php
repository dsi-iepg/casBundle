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

class CasAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private $router;
    private String $cas_host;
    private String $cas_path;
    private Int $cas_port;
    private Bool $cas_ca;
    private String $cas_ca_path;
    private String $cas_dispatcher_name;
    private String $cas_user_unknow;

    public function __construct(
        String $cas_host,
        String $cas_path,
        Int $cas_port,
        Bool $cas_ca,
        String $cas_ca_path,
        String $cas_dispatcher_name,
        String $cas_user_unknow,
        UrlGeneratorInterface $router
    ) {
        $this->router = $router;
        $this->cas_host = $cas_host;
        $this->cas_path = $cas_path;
        $this->cas_port = $cas_port;
        $this->cas_ca = $cas_ca;
        $this->cas_ca_path = $cas_ca_path;
        $this->cas_dispatcher_name = $cas_dispatcher_name;
        $this->cas_user_unknow = $cas_user_unknow;
    }

    public function supports(Request $request): ?bool
    {
        return ($request->getMethod() === 'GET' &&
            $request->attributes->get('_route') === 'cas_login'
        );
    }

    public function authenticate(Request $request): Passport
    {
        $cas_path = (empty($cas_path)) ?
            $request->getSchemeAndHttpHost()
            :
            $this->cas_path
        ;

        \phpCAS::setVerbose(false);
        \phpCAS::client(
            CAS_VERSION_2_0,
            $this->cas_host,
            $this->cas_port,
            '',
            $cas_path
        );
        \phpCAS::handleLogoutRequests();
        if ($this->cas_ca) {
            \phpCAS::setCasServerCACert(realpath($this->cas_ca_path));
        } else {
            \phpCAS::setNoCasServerValidation();
        }
        \phpCAS::setLang(PHPCAS_LANG_FRENCH);
        \phpCAS::forceAuthentication();
        \phpCAS::checkAuthentication();

        $user = $_SESSION['cas_user'] = \phpCAS::getUser();

        $_SESSION['cas_ticket'] = md5(random_bytes(32));
        $_SESSION['cas_attributes'] = \phpCAS::getAttributes();

        return new SelfValidatingPassport(new UserBadge($_SESSION['cas_user']));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $target = (null == $this->cas_dispatcher_name)? 'cas_dispatcher' : $this->cas_dispatcher_name;
        return new RedirectResponse(
            $this->router->generate($target)
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (null != $_SESSION['cas_user']) {
            $this->cas_user_unknow = (null == $this->cas_user_unknow)? 'cas_user_unknow' : $this->cas_user_unknow;
            
            (null == $this->cas_user_unknow)? 'cas_user_unknow' : $this->cas_user_unknow;

            return new RedirectResponse(
                $this->router->generate($this->cas_user_unknow)
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
}
