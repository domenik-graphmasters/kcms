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

use vcms\menu\LibMenuElement;
use vcms\menu\LibMenuEntry;
use vcms\menu\LibMenuEntryExternalLink;
use vcms\menu\LibMenuEntryLogin;
use vcms\menu\LibMenuFolder;
use vcms\module\LibAccessRestriction;
use vcms\module\LibInclude;
use vcms\module\LibModule;
use vcms\module\LibPage;

class LibModuleParser
{
    var $defaultMenuEntryPosition = 65535;

    var $defaultPageContainerEnabled = true;

    function parseMetaJson(
        string $moduleDirectory,
        string $moduleRelativePath
    ): LibModule {
        global $libGlobal, $libFilesystem;

        $moduleAbsolutePath = $libFilesystem->getAbsolutePath(
            $moduleRelativePath
        );
        $jsonAbsolutePath = $moduleAbsolutePath . "/meta.json";

        $jsonFileContents = file_get_contents($jsonAbsolutePath);
        $json = json_decode($jsonFileContents, true);

        if (json_last_error()) {
            $errorMessage = json_last_error_msg();
            $libGlobal->errorTexts[] =
                "Fehler in " . $jsonAbsolutePath . ": " . $errorMessage;
        } else {
            if (isset($json["version"]) && !is_numeric($json["version"])) {
                $libGlobal->errorTexts[] =
                    "Versionsangabe nicht numerisch in Modul " .
                    $moduleRelativePath;
            }

            if (!isset($json["moduleName"]) || $json["moduleName"] == "") {
                $libGlobal->errorTexts[] =
                    "Kein moduleName in Modul " . $moduleRelativePath;
            }

            /*
             * determine module parameters
             */
            $version = isset($json["version"]) ? $json["version"] : "";
            $moduleName = isset($json["moduleName"]) ? $json["moduleName"] : "";
            $installScript = isset($json["installScript"])
                ? $json["installScript"]
                : "";
            $uninstallScript = isset($json["uninstallScript"])
                ? $json["uninstallScript"]
                : "";
            $updateScript = isset($json["updateScript"])
                ? $json["updateScript"]
                : "";
            $headerStrings = isset($json["headerStrings"])
                ? $json["headerStrings"]
                : [];

            $pages = [];
            $includes = [];
            $menuElementsInternet = [];
            $menuElementsIntranet = [];
            $menuElementsAdministration = [];

            if (isset($json["pages"]) && is_array($json["pages"])) {
                foreach ($json["pages"] as $pageJson) {
                    $page = $this->parsePageJson($pageJson);
                    $pages[$page->getPid()] = $page;
                }
            }

            if (isset($json["includes"]) && is_array($json["includes"])) {
                foreach ($json["includes"] as $includeJson) {
                    $include = $this->parseIncludeJson($includeJson);
                    $includes[$include->getIid()] = $include;
                }
            }

            if (
                isset($json["menuElementsInternet"]) &&
                is_array($json["menuElementsInternet"])
            ) {
                foreach (
                    $json["menuElementsInternet"]
                    as $menuElementInternetJson
                ) {
                    $menuElementsInternet[] = $this->parseMenuElement(
                        $menuElementInternetJson
                    );
                }
            }

            if (
                isset($json["menuElementsIntranet"]) &&
                is_array($json["menuElementsIntranet"])
            ) {
                foreach (
                    $json["menuElementsIntranet"]
                    as $menuElementsIntranetJson
                ) {
                    $menuElementsIntranet[] = $this->parseMenuElement(
                        $menuElementsIntranetJson
                    );
                }
            }

            if (
                isset($json["menuElementsAdministration"]) &&
                is_array($json["menuElementsAdministration"])
            ) {
                foreach (
                    $json["menuElementsAdministration"]
                    as $menuElementsAdministrationJson
                ) {
                    $menuElementsAdministration[] = $this->parseMenuElement(
                        $menuElementsAdministrationJson
                    );
                }
            }

            // instantiate new module
            $module = new LibModule(
                $moduleDirectory,
                $moduleName,
                $version,
                $moduleRelativePath,
                $pages,
                $includes,
                $headerStrings,
                $installScript,
                $uninstallScript,
                $updateScript,
                $menuElementsInternet,
                $menuElementsIntranet,
                $menuElementsAdministration
            );
            return $module;
        }
    }

    /**
     * @param array $pageJson
     * @return LibPage
     */
    function parsePageJson(array $pageJson): LibPage
    {
        $pid = isset($pageJson["pid"]) ? $pageJson["pid"] : "";
        $file = isset($pageJson["file"]) ? $pageJson["file"] : "";
        $directory = isset($pageJson["directory"])
            ? $pageJson["directory"]
            : "";
        $accessRestriction = isset($pageJson["accessRestriction"])
            ? $this->parseAccessRestrictionJson($pageJson["accessRestriction"])
            : "";
        $title = isset($pageJson["title"]) ? $pageJson["title"] : "";
        $containerEnabled = isset($pageJson["containerEnabled"])
            ? boolval($pageJson["containerEnabled"])
            : $this->defaultPageContainerEnabled;

        $page = new \vcms\module\LibPage(
            $pid,
            $directory,
            $file,
            $accessRestriction,
            $title,
            $containerEnabled
        );
        return $page;
    }

    /**
     * @param array $includeJson
     * @return \vcms\module\LibInclude
     */
    function parseIncludeJson(array $includeJson): LibInclude
    {
        $iid = isset($includeJson["iid"]) ? $includeJson["iid"] : "";
        $file = isset($includeJson["file"]) ? $includeJson["file"] : "";
        $directory = isset($includeJson["directory"])
            ? $includeJson["directory"]
            : "";
        $accessRestriction = isset($includeJson["accessRestriction"])
            ? $this->parseAccessRestrictionJson(
                $includeJson["accessRestriction"]
            )
            : "";

        $include = new \vcms\module\LibInclude(
            $iid,
            $directory,
            $file,
            $accessRestriction
        );
        return $include;
    }

    /**
     * @param array $accessRestrictionJson
     * @return \vcms\module\LibAccessRestriction
     */
    function parseAccessRestrictionJson(
        array $accessRestrictionJson
    ): LibAccessRestriction {
        $aemter = isset($accessRestrictionJson["aemter"])
            ? $accessRestrictionJson["aemter"]
            : "";
        $gruppen = isset($accessRestrictionJson["gruppen"])
            ? $accessRestrictionJson["gruppen"]
            : "";

        $accessRestriction = new \vcms\module\LibAccessRestriction(
            $gruppen,
            $aemter
        );
        return $accessRestriction;
    }

    /**
     * @param array $menuElementJson
     * @return \vcms\menu\LibMenuElement
     */
    function parseMenuElement(array $menuElementJson): LibMenuElement
    {
        $type = isset($menuElementJson["type"]) ? $menuElementJson["type"] : "";

        switch ($type) {
            case "menu_entry":
                $menuElement = $this->parseMenuEntry($menuElementJson);
                break;
            case "menu_entry_login":
                $menuElement = $this->parseMenuEntryLogin($menuElementJson);
                break;
            case "menu_entry_external_link":
                $menuElement = $this->parseMenuEntryExternalLink(
                    $menuElementJson
                );
                break;
            case "menu_folder":
                $menuElement = $this->parseMenuFolder($menuElementJson);
                break;
        }

        return $menuElement;
    }

    /**
     * @param array $menuElementJson
     * @return \vcms\menu\LibMenuEntry
     */
    function parseMenuEntry(array $menuElementJson): LibMenuEntry
    {
        $pid = isset($menuElementJson["pid"]) ? $menuElementJson["pid"] : "";
        $name = isset($menuElementJson["name"]) ? $menuElementJson["name"] : "";
        $position = isset($menuElementJson["position"])
            ? $menuElementJson["position"]
            : $this->defaultMenuEntryPosition;

        $menuEntry = new \vcms\menu\LibMenuEntry($pid, $name, $position);
        return $menuEntry;
    }

    /**
     * @param array $menuElementJson
     * @return \vcms\menu\LibMenuEntryLogin
     */
    function parseMenuEntryLogin(array $menuElementJson): LibMenuEntryLogin
    {
        $pid = isset($menuElementJson["pid"]) ? $menuElementJson["pid"] : "";
        $name = isset($menuElementJson["name"]) ? $menuElementJson["name"] : "";
        $nameLogout = isset($menuElementJson["nameLogout"])
            ? $menuElementJson["nameLogout"]
            : "";
        $position = isset($menuElementJson["position"])
            ? $menuElementJson["position"]
            : $this->defaultMenuEntryPosition;

        $menuEntry = new \vcms\menu\LibMenuEntryLogin(
            $pid,
            $name,
            $nameLogout,
            $position
        );
        return $menuEntry;
    }

    /**
     * @param array $menuElementJson
     * @return \vcms\menu\LibMenuEntryExternalLink
     */
    function parseMenuEntryExternalLink(
        array $menuElementJson
    ): LibMenuEntryExternalLink {
        $pid = isset($menuElementJson["pid"]) ? $menuElementJson["pid"] : "";
        $name = isset($menuElementJson["name"]) ? $menuElementJson["name"] : "";
        $position = isset($menuElementJson["position"])
            ? $menuElementJson["position"]
            : $this->defaultMenuEntryPosition;

        $menuEntry = new \vcms\menu\LibMenuEntryExternalLink(
            $pid,
            $name,
            $position
        );
        return $menuEntry;
    }

    /**
     * @param array $menuFolderJson
     * @return \vcms\menu\LibMenuFolder
     */
    function parseMenuFolder(array $menuFolderJson): LibMenuFolder
    {
        $pid = isset($menuFolderJson["pid"]) ? $menuFolderJson["pid"] : "";
        $name = isset($menuFolderJson["name"]) ? $menuFolderJson["name"] : "";
        $position = isset($menuFolderJson["position"])
            ? $menuFolderJson["position"]
            : $this->defaultMenuEntryPosition;

        $menuFolder = new LibMenuFolder($pid, $name, $position);

        if (
            isset($menuFolderJson["elements"]) &&
            is_array($menuFolderJson["elements"])
        ) {
            foreach ($menuFolderJson["elements"] as $menuElementJson) {
                $menuElement = $this->parseMenuElement($menuElementJson);
                $menuFolder->addElement($menuElement);
            }
        }

        return $menuFolder;
    }
}
