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
use CPSIT\Auditor\Reflection\InstallPathLocator;
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
    use DescriberTrait;
    static protected $properties = %s;
    
    private function __construct()
    {
    }
    
    public static function getProperty(string $key) {
        if (!isset (self::$properties[$key])) {
            throw new \OutOfBoundsException(
                'Required key "' . $key . '" is not valid: property not found in package'
            );
        }
    }
}

PHP;

    /**
     * @var InstallPathLocator
     */
    protected $installPathLocator;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * BundleDescriberClassGenerator constructor.
     * @param Composer $composer
     */
    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->installPathLocator = new InstallPathLocator($composer);
        $this->io = $io;
    }

    public function writeFile(array $properties = []) {
        $filePath = $this->getFilePath();


        if (!file_exists(dirname($filePath))) {
            $this->getIo()->write(self::MESSAGE_INFO_LEAD . self::ERROR_ROOT_PACKAGE_NOT_FOUND);
            return;
        }

        file_put_contents($filePath, $this->generateSource($properties));
        chmod($filePath, 0664);

        $this->getIo()->write(self::MESSAGE_INFO_LEAD . self::MESSAGE_DONE_BUNDLE_DESCRIBER);
    }

    /**
     * @return InstallPathLocator
     */
    public function getInstallPathLocator(): InstallPathLocator {
        return $this->installPathLocator;
    }

    /**
     * @return IOInterface
     */
    public function getIo(): IOInterface
    {
        return $this->io;
    }

    /**
     * Generates the source for the BundleDescriber class
     * @return string
     */
    protected function generateSource($properties): string {
        return sprintf(
            self::$generatedClassTemplate,
            'fin' . 'al ' . 'cla' . 'ss ' . SI::BUNDLE_DESCRIBER_CLASS, // note: workaround for regex-based code parsers :-(
            var_export($properties, true)
        );
    }

    /**
     * Get the path of the Application Reflection Class
     * @return string
     */
    protected function getFilePath(): string
    {
        return $this->getInstallPathLocator()->getInstallPath() . '/'
            . SI::SOURCE_FOLDER_NAME . '/' . SI::BUNDLE_DESCRIBER_CLASS . '.php';
    }

}
