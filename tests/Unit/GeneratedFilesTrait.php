<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "cpsit/auditor".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace CPSIT\Auditor\Tests\Unit;

use CPSIT\Auditor\SettingsInterface as SI;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Trait to handle files generated by this Composer plugin in Unit tests.
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
trait GeneratedFilesTrait
{
    protected static $generatedFiles = [
        SI::SOURCE_FOLDER_NAME . '/' . SI::BUNDLE_DESCRIBER_CLASS . '.php',
        '.build/vendor/composer/InstalledVersions.php',
        '.build/vendor/composer/package-versions-deprecated/src/PackageVersions/Versions.php',
    ];

    protected static function cleanUpGeneratedFiles(): void
    {
        $files = array_map(function (string $file) {
            return dirname(__DIR__, 3) . '/' . $file;
        }, static::$generatedFiles);

        $filesystem = new Filesystem();
        $filesystem->remove($files);
    }
}
