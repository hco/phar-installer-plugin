<?php


namespace hco\ComposerPharInstaller;


use Composer\Composer;
use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Composer\Repository\InstalledRepositoryInterface;

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

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $binDir = rtrim($this->composer->getConfig()->get('bin-dir'), '/');
        $this->filesystem->ensureDirectoryExists($binDir);

        $packageExtra = $package->getExtra();

        $phars = glob($this->getInstallPath($package) . '/*.phar');
        $pathToPhar = realpath(reset($phars));

        if(isset($packageExtra['bin-name'])) {
            $link = $binDir . '/' . $packageExtra['bin-name'];
        } else {
            $link = $binDir . '/' . basename($pathToPhar);
        }
        $relativeBin = $this->filesystem->findShortestPath($link, $pathToPhar);

        if (file_exists($link)) {
            unlink($link);
        }

        if (false === symlink($relativeBin, $link)) {
            throw new \ErrorException();
        }

        @chmod($link, 0777 & ~umask());
    }
}
