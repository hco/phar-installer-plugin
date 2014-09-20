<?php

namespace hco\ComposerPharInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;

class PharInstallerPlugin implements \Composer\Plugin\PluginInterface
{

    /**
     * Apply plugin modifications to composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new PharInstaller(
            $io,
            $composer
        );

        $composer->getInstallationManager()->addInstaller($installer);
    }
}
