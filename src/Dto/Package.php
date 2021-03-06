<?php

namespace CPSIT\Auditor\Dto;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel <wenzel@cps-it.de>
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
class Package
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $version = '';

    /**
     * @var string
     */
    protected $sourceReference = '';

    public function __construct(array $info = [])
    {
        if (!empty($info['version'])) {
            $this->version = $info['version'];
        }
        if (!empty($info['name'])) {
            $this->name = $info['name'];
        }

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Package
     */
    public function setName(string $name): Package
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Package
     */
    public function setVersion(string $version): Package
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceReference(): string
    {
        return $this->sourceReference;
    }

    /**
     * @param string $sourceReference
     * @return Package
     */
    public function setSourceReference(string $sourceReference): Package
    {
        $this->sourceReference = $sourceReference;

        return $this;
    }
}
