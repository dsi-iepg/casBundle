<?php
namespace Iepg\Bundle\Cas\Controller;

use Symfony\Component\HttpFoundation\Request;

class CasLogout
{
    public function __construct(
        private readonly array $casConfig
    ) {}

    public function logout(Request $request): void
    {
        \phpCAS::client(
            CAS_VERSION_2_0,
            $this->casConfig['cas_host'],
            (int) $this->casConfig['cas_port'],
            $this->casConfig['cas_path']
        );

        if ($this->casConfig['cas_ca'] === false) {
            \phpCAS::setNoCasServerValidation();
        }

        if (\phpCAS::isAuthenticated()) {
            \phpCAS::logout();
        }
    }
}