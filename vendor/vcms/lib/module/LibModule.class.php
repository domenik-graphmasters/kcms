<?php
/*
This file is part of VCMS.

VCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

VCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with VCMS. If not, see <http://www.gnu.org/licenses/>.
*/

namespace vcms\module;

use vcms\menu\LibMenuElement;

readonly class LibModule
{
    /**
     * @param LibPage[] $pages
     * @param LibInclude[] $includes
     * @param $headerStrings,
     * @param LibMenuElement[] $menuElementsInternet
     * @param LibMenuElement[] $menuElementsIntranet
     * @param LibMenuElement[] $menuElementsAdministration
     */
    function __construct(
        string $id,
        string $name,
        int $version,
        string $path,
        array $pages,
        array $includes,
        array $headerStrings,
        string $installScript,
        string $uninstallScript,
        string $updateScript,
        array $menuElementsInternet,
        array $menuElementsIntranet,
        array $menuElementsAdministration
    ) {
        global $libGlobal;

        if ($id == "") {
            $libGlobal->errorTexts[] = "Fehlende Module-Id";
        }

        if ($version != "" && !is_numeric($version)) {
            $libGlobal->errorTexts[] = "Versionsangabe nicht numerisch";
        }

        if ($name == "") {
            $libGlobal->errorTexts[] = "Fehlende Namensangabe";
        }

        if ($path == "") {
            $libGlobal->errorTexts[] = "Fehlender Modulpfad";
        }
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getVersion(): string
    {
        return $this->version;
    }

    function getPath(): string
    {
        return $this->path;
    }

    function getPages(): array
    {
        return $this->pages;
    }

    function getIncludes(): array
    {
        return $this->includes;
    }

    function getInstallScript(): string
    {
        return $this->installScript;
    }

    function getUninstallScript(): string
    {
        return $this->uninstallScript;
    }

    function getUpdateScript(): string
    {
        return $this->updateScript;
    }

    /**
     * @return LibMenuElement[]
     */
    function getMenuElementsInternet(): array
    {
        return $this->menuElementsInternet;
    }

    /**
     * @return LibMenuElement[]
     */
    function getMenuElementsIntranet(): array
    {
        return $this->menuElementsIntranet;
    }

    /**
     * @return LibMenuElement[]
     */
    function getMenuElementsAdministration(): array
    {
        return $this->menuElementsAdministration;
    }

    function getHeaderStrings(): array
    {
        return $this->headerStrings;
    }
}
