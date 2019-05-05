<?php

namespace CPSIT\Auditor;

use CPSIT\Auditor\Dto\Package;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel
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

/**
 * Trait InstalledPackagesTrait
 */
trait InstalledPackagesTrait
{
    /**
     * @return array
     */
    static public function getInstalledPackages(): array
    {
        if (self::propertyExists(DescriberInterface::INSTALLED_PACKAGES)) {
            return self::$installedPackages;
        }

        return [];
    }

    /**
     * @param string $propertyName
     * @return bool
     */
    static public function propertyExists(string $propertyName): bool
    {
        if (!property_exists(self::class, DescriberInterface::INSTALLED_PACKAGES)) {
            throw new \OutOfBoundsException(
                sprintf(DescriberInterface::ERROR_MISSING_PROPERTY, $propertyName, self::class),
                1557047757
            );
        }


        return true;
    }

    /**
     * Get a representation of a installed package
     * @param string $name
     * @return Package|null
     */
    static public function getInstalledPackage(string $name): Package
    {
        if (!self::isPackageInstalled($name)) {
            return null;
        }

        $package = new Package(self::$installedPackages[$name]);

        return $package;

    }

    /**
     * Tells whether a package is installed
     *
     * @param string $name Package Name
     * @return bool
     */
    static public function isPackageInstalled(string $name): bool
    {
        self::propertyExists(DescriberInterface::INSTALLED_PACKAGES);
        return (array_key_exists($name, self::$installedPackages));
    }
}