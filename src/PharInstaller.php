<?php


namespace hco\ComposerPharInstaller;


use Composer\Composer;
use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

class PharInstaller extends LibraryInstaller implements InstallerInterface
{
    public function __construct(IOInterface $io, Composer $composer, Filesystem $filesystem = null)
    {
        parent::__construct(
            $io,
            $composer,
            'toolphar',
            $filesystem
        );
    }

    protected function installBinaries(PackageInterface $package)
    {
        $this->initializeBinDir();
        parent::installBinaries($package);
        $packageExtra = $package->getExtra();

        $phars = glob($this->getInstallPath($package) . '/*.phar');
        $pathToPhar = realpath(reset($phars));

        if(isset($packageExtra['bin-name'])) {
            $link = $this->binDir . '/' . $packageExtra['bin-name'];
        } else {
            $link = $this->binDir . '/' . basename($pathToPhar);
        }
        $relativeBin = $this->filesystem->findShortestPath($link, $pathToPhar);


        if (false === symlink($relativeBin, $link)) {
            throw new \ErrorException();
        }

        @chmod($link, 0777 & ~umask());
    }
}
