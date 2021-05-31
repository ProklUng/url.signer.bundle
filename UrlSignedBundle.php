<?php

namespace Prokl\UrlSignedBundle;

use Prokl\UrlSignedBundle\DependencyInjection\Compiler\SignerPass;
use Prokl\UrlSignedBundle\DependencyInjection\UrlSignedExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class UrlSignedBundle
 * @package Prokl\UrlSignedBundle
 *
 * @since 12.02.2021
 */
class UrlSignedBundle extends Bundle
{
   /**
   * @inheritDoc
   */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new UrlSignedExtension();
        }

        return $this->extension;
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SignerPass());
    }
}
