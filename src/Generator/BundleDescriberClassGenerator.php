<?php
namespace CPSIT\Auditor\Generator;

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
use Composer\IO\IOInterface;
use CPSIT\Auditor\Reflection\InstallPath;
use CPSIT\Auditor\SettingsInterface as SI;

/**
 * Class BundleDescriberClassGenerator
 */
class BundleDescriberClassGenerator
{
    public const MESSAGE_INFO_LEAD = '<info>' . SI::PACKAGE_IDENTIFIER . '</info> :';
    public const ERROR_ROOT_PACKAGE_NOT_FOUND = ' Package not found (probably scheduled for removal); generation of application reflection class skipped.';
    public const MESSAGE_GENERATE_BUNDLE_DESCRIBER = ' Generate bundle describer class...';
    public const MESSAGE_DONE_BUNDLE_DESCRIBER = ' bundle describer class generated';

    private static $generatedClassTemplate = <<<'PHP'
<?php

namespace CPSIT\Auditor;

/**
 * This class is generated by cpsit/auditor, specifically by
 * @see \Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
%s implements DescriberInterface
{
    use PropertiesTrait;
    static protected $properties = %s;
    static protected $installedPackages = %s;
    
    private function __construct()
    {
    }    
}

PHP;

    /**
     * @var InstallPath
     */
    protected $installPath;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * BundleDescriberClassGenerator constructor.
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->installPath = new InstallPath($composer);
        $this->io = $io;
    }

    /**
     * @param array $properties
     * @param array $installedPackages
     */
    public function writeFile(array $properties = [], array $installedPackages = []) {
        $filePath = $this->getFilePath();


        if (!file_exists(dirname($filePath))) {
            $this->io->write(self::MESSAGE_INFO_LEAD . self::ERROR_ROOT_PACKAGE_NOT_FOUND);
            return;
        }

        file_put_contents($filePath, $this->generateSource($properties, $installedPackages));
        chmod($filePath, 0664);

        $this->io->write(self::MESSAGE_INFO_LEAD . self::MESSAGE_DONE_BUNDLE_DESCRIBER);
    }

    /**
     * Generates the source for the BundleDescriber class
     * @param array $properties
     * @param array $installedPackages
     * @return string
     */
    protected function generateSource(array $properties, array $installedPackages): string {
        return sprintf(
            self::$generatedClassTemplate,
            'fin' . 'al ' . 'cla' . 'ss ' . SI::BUNDLE_DESCRIBER_CLASS, // note: workaround for regex-based code parsers :-(
            var_export($properties, true),
            var_export($installedPackages, true)
        );
    }

    /**
     * Get the path of the Application Reflection Class
     * @return string
     */
    protected function getFilePath(): string
    {
        return $this->installPath->toString() . '/'
            . SI::SOURCE_FOLDER_NAME . '/' . SI::BUNDLE_DESCRIBER_CLASS . '.php';
    }

}
