<?php

namespace Cpsit\Conductor;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\AliasPackage;
use Composer\Package\Locker;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Cpsit\Conductor\SettingsInterface as SI;

/**
 * Class Installer
 */
final class Installer implements PluginInterface, EventSubscriberInterface
{

    private const MESSAGE_INFO_LEAD = '<info>' . SI::PACKAGE_IDENTIFIER . '</info>';
    public const ERROR_ROOT_PACKAGE_NOT_FOUND = 'Package not found (probably scheduled for removal); generation of application reflection class skipped.';
    public const MESSAGE_GENERATE_APPLICATION_REFLECTION = 'Generate application reflection class...';
    public const MESSAGE_DONE_APPLICATION_REFLECTION = '...done generating application reflection class';

    private static $generatedClassTemplate = <<<'PHP'
<?php

namespace Cpsit\Conductor;

/**
 * This class is generated by cpsit/conductor, specifically by
 * @see \Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
%s
{
    const ROOT_PACKAGE_NAME = '%s';
    const VERSIONS = %s;

    private function __construct()
    {
    }
}

PHP;

    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        // Nothing to do here, as all features are provided through event listeners
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'dumpApplicationReflectionClass',
            ScriptEvents::POST_UPDATE_CMD => 'dumpApplicationReflectionClass',
        ];
    }

    public static function dumpApplicationReflectionClass(Event $composerEvent)
    {
        $composer = $composerEvent->getComposer();
        $rootPackage = $composer->getPackage();
        $reflectionClass = self::generateApplicationReflectionClass($rootPackage->getName());

        self::writeApplicationReflectionToFile($reflectionClass, $composer, $composerEvent->getIO());
    }

    /**
     * @throws \RuntimeException
     */
    private static function writeApplicationReflectionToFile(string $reflectionClassSource, Composer $composer, IOInterface $io): void
    {
        $installPath = self::locateRootPackageInstallPath($composer->getConfig(), $composer->getPackage())
            . '/src/ApplicationReflection.php';

        if (!file_exists(dirname($installPath))) {
            $io->write(self::MESSAGE_INFO_LEAD . self::ERROR_ROOT_PACKAGE_NOT_FOUND);
            return;
        }
        $io->write(self::MESSAGE_INFO_LEAD . self::MESSAGE_GENERATE_APPLICATION_REFLECTION);

        file_put_contents($installPath, $versionClassSource);
        chmod($installPath, 0664);

        $io->write(self::MESSAGE_INFO_LEAD . self::MESSAGE_DONE_APPLICATION_REFLECTION);
    }

    /**
     * @throws \RuntimeException
     */
    private static function locateRootPackageInstallPath(
        Config $composerConfig,
        RootPackageInterface $rootPackage
    ): string
    {
        if (SI::PACKAGE_IDENTIFIER === self::getRootPackageAlias($rootPackage)->getName()) {
            return dirname($composerConfig->get(SI::KEY_VENDOR_DIR));
        }

        return $composerConfig->get(SI::KEY_VENDOR_DIR) . '/' . SI::PACKAGE_IDENTIFIER;
    }

    private static function getRootPackageAlias(RootPackageInterface $rootPackage): PackageInterface
    {
        $package = $rootPackage;

        while ($package instanceof AliasPackage) {
            $package = $package->getAliasOf();
        }

        return $package;
    }

    private static function generateApplicationReflectionClass(string $rootPackageName): string
    {
        return sprintf(
            self::$generatedClassTemplate,
            'fin' . 'al ' . 'cla' . 'ss ' . 'ApplicationReflection', // note: workaround for regex-based code parsers :-(
            $rootPackageName
        );
    }


}
