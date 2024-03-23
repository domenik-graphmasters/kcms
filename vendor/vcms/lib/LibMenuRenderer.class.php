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

namespace vcms;

use vcms\menu\LibMenuFolder;

class LibMenuRenderer
{

    private const ELEMENT_TYPE_INTERNAL_LINK = 1;

    private const ELEMENT_TYPE_FOLDER = 2;

    private const ELEMENT_TYPE_EXTERNAL_LINK = 3;

    private const ELEMENT_TYPE_LOGIN = 4;

    var $defaultIndent = '            ';

    function printNavbar($menuInternet, $menuIntranet, $menuAdministration, $aktivesPid, $gruppe, $aemter)
    {
        global $libGenericStorage;
        global $libAuth;

        $menuInternet = $menuInternet->copy();
        $menuInternet->reduceByAccessRestriction($gruppe, $aemter);

        $menuIntranet = $menuIntranet->copy();
        $menuIntranet->reduceByAccessRestriction($gruppe, $aemter);

        $menuAdministration = $menuAdministration->copy();
        $menuAdministration->reduceByAccessRestriction($gruppe, $aemter);

        $navbarClass = $this->getNavbarClass();

        echo '    <nav id="nav" class="navbar navbar-expand-xl navbar-default navbar-light bg-light navbar-fixed-top ' . $navbarClass . '">' . PHP_EOL;
        echo '      <div class="container">' . PHP_EOL;
        $brand = $libGenericStorage->loadValue('base_core', 'brand');
        $brandShort = $libGenericStorage->loadValue('base_core', 'brand_xs');
        echo '    <a class="navbar-brand" href="index.php">';
        echo '    <div id="logo" class="d-inline-block align-middle"></div>';
        echo "<span class='d-inline d-xl-none'>$brandShort</span>";
        echo "<span class='d-none d-xl-inline'>$brand</span>";
        echo '</a>';
        $this->printNavbarCollapsed();
        echo '<div class="collapse navbar-collapse flex-md-column w-100" id="navbar-internet">';
        echo '<ul class="navbar-nav ms-auto mb-2 mb-md-0">';
        $this->renderMenuFolder($menuInternet->getRootMenuFolder(), $aktivesPid);
        echo '</ul>';
        if ($libAuth->isLoggedIn()) {
            echo '<hr class="d-xl-none"/>';
            echo '<ul class="navbar-nav ms-auto mb-2 mb-md-0">';
            $this->renderMenuFolder($menuIntranet->getRootMenuFolder(), $aktivesPid);
            $this->renderMenuFolder($menuAdministration->getRootMenuFolder(), $aktivesPid);
            echo '</ul>';
        }
        echo '</div>';
        echo '      </div>' . PHP_EOL;
        echo '    </nav>' . PHP_EOL;
    }

    private function printNavbarCollapsed(): void
    {
        echo '        <div class="navbar-header">' . PHP_EOL;
        echo '          <button type="button" class="navbar-toggler collapsed" data-bs-toggle="collapse" data-bs-target="#navbar-internet,#navbar-intranet" aria-expanded="false">' . PHP_EOL;
        echo $this->defaultIndent . '<span class="navbar-toggler-icon"></span>' . PHP_EOL;
        echo '          </button>' . PHP_EOL;
        echo '        </div>' . PHP_EOL;
    }

    private function renderMenuFolder(LibMenuFolder $folder, string $activePid): void
    {
        global $libAuth;

        foreach ($folder->getElements() as $item) {
            $active = '';
            if ($item->getPid() == $activePid) {
                $active = ' active';
            }

            $targetPid = $item->getPid();
            $targetName = $item->getName();

            switch ($item->getType()) {
                case LibMenuRenderer::ELEMENT_TYPE_INTERNAL_LINK:
                    echo "<li class='nav-item'><a class='nav-link$active' href='index.php?pid=$targetPid'>$targetName</a></li>" . PHP_EOL;
                    break;
                case LibMenuRenderer::ELEMENT_TYPE_FOLDER:
                    echo '<li class="nav-item dropdown">' . PHP_EOL;
                    echo "<a class='nav-link dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>$targetName</a>";
                    echo '<div class="dropdown-menu">' . PHP_EOL;
                    foreach ($item->getElements() as $dropdownElement) {
                        $dropdownElementPid = $dropdownElement->getPid();
                        $dropdownElementName = $dropdownElement->getName();
                        echo "<a class='dropdown-item' href='index.php?pid=$dropdownElementPid'>$dropdownElementName</a>";
                    }
                    echo '</div>';
                    echo '</li>' . PHP_EOL;
                    break;
                case LibMenuRenderer::ELEMENT_TYPE_EXTERNAL_LINK:
                    echo "<li class='nav-item'><a class='nav-link$active' href='index.php?pid=$targetPid'><i class='fa fa-external-link' aria-hidden='true'></i>$targetName</a></li>" . PHP_EOL;
                    break;
                case LibMenuRenderer::ELEMENT_TYPE_LOGIN:
                    echo "<li class='nav-item'>";
                    if (!$libAuth->isLoggedin()) {
                        echo "<a class='nav-link$active' href='index.php?pid=$targetPid'>$targetName</a>";
                    } else {
                        $logoutName = $item->getNameLogout();
                        echo "<a class='nav-link$active' href='index.php?logout=1'>$logoutName</a>";
                    }
                    echo '</li>';
                    break;
                default:
                    break;
            }
        }
    }

    function getNavbarClass(): string
    {
        global $libAuth;

        return !$libAuth->isLoggedin() ? 'navbar-internet-only' : 'navbar-internet-intranet';
    }
}
