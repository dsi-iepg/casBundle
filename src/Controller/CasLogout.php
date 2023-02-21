<?php

namespace Iepg\Bundle\Cas\Controller;

class CasLogout
{
    private String $cas_host;
    private String $cas_path;
    private Int $cas_port;
    private Bool $cas_ca;
    private String $cas_ca_path;

    public function __construct(
        String $cas_host,
        String $cas_path,
        Int $cas_port,
        Bool $cas_ca,
        String $cas_ca_path
    ) {
        $this->cas_host = $cas_host;
        $this->cas_path = $cas_path;
        $this->cas_port = $cas_port;
        $this->cas_ca = $cas_ca;
        $this->cas_ca_path = $cas_ca_path;
    }


    public function logout($request): void
    {
        \phpCAS::setVerbose(false);
        $cas_path = (empty($cas_ca_path)) ?
            $request->getSchemeAndHttpHost()
            :
            $this->cas_path;
        \phpCAS::client(
            CAS_VERSION_2_0,
            $this->cas_host,
            $this->cas_port,
            '',
            $cas_path
        );
        \phpCAS::handleLogoutRequests();
        \phpCAS::logout();
        \phpCAS::forceAuthentication();
    }
}
