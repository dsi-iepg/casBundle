<?php
namespace Iepg\Bundle\Cas;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Iepg\Bundle\Cas\DependencyInjection\CasConnectionExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CasConnectionBundle extends Bundle
{
    public function getContainerExtension(): ?CasConnectionExtension
    {
        if (null === $this->extension) {
            $this->extension = new CasConnectionExtension();
        }
        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $ext = new CasConnectionExtension([], $container);
    }
}