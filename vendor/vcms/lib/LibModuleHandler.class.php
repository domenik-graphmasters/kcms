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

class LibModuleHandler
{
    var $modModulesRelativePath = "modules";
    var $baseModulesRelativePath = "vendor/vcms/modules";

    var $modules = [];
    var $pages = [];
    var $includes = [];
    var $pidToModulePointer = [];
    var $iidToModulePointer = [];

    var $menuInternet;
    var $menuIntranet;
    var $menuAdministration;

    function __construct()
    {
        $this->menuInternet = new \vcms\menu\LibMenu();
        $this->menuIntranet = new \vcms\menu\LibMenu();
        $this->menuAdministration = new \vcms\menu\LibMenu();
    }

    /**
     * Returns an array of all module files in the mod directory.
     *
     * @return array
     */
    function getModModuleFiles(): array
    {
        global $libFilesystem;

        $modModulesAbsolutePath = $libFilesystem->getAbsolutePath(
            $this->modModulesRelativePath
        );
        $result = array_diff(scandir($modModulesAbsolutePath), [".", ".."]);
        sort($result);
        return $result;
    }

    /**
     * Returns an array of all module files in the base directory.
     *
     * @return array
     */
    function getBaseModuleFiles(): array
    {
        global $libFilesystem;

        $baseModulesAbsolutePath = $libFilesystem->getAbsolutePath(
            $this->baseModulesRelativePath
        );
        $result = array_diff(scandir($baseModulesAbsolutePath), [".", ".."]);
        sort($result);
        return $result;
    }

    /**
     * Initializes all modules.
     */
    function initModules(): void
    {
        global $libFilesystem;

        $modModuleFiles = $this->getModModuleFiles();
        $baseModuleFiles = $this->getBaseModuleFiles();
        $moduleAbsolutePaths = [];
        $moduleRelativePaths = [];

        foreach ($modModuleFiles as $moduleFile) {
            $moduleRelativePaths[$moduleFile] =
                $this->modModulesRelativePath . "/" . $moduleFile;
        }

        foreach ($baseModuleFiles as $moduleFile) {
            $moduleRelativePaths[$moduleFile] =
                $this->baseModulesRelativePath . "/" . $moduleFile;
        }

        foreach ($moduleRelativePaths as $moduleFile => $moduleRelativePath) {
            $moduleAbsolutePath = $libFilesystem->getAbsolutePath(
                $moduleRelativePath
            );

            if (is_dir($moduleAbsolutePath)) {
                $this->initModule($moduleFile, $moduleRelativePath);
            }
        }
    }

    /**
     * Initializes a single module.
     *
     * @param string $moduleDirectory
     * @param string $moduleRelativePath
     */
    function initModule(
        string $moduleDirectory,
        string $moduleRelativePath
    ): void {
        global $libGlobal, $libFilesystem, $libModuleParser;

        $moduleAbsolutePath = $libFilesystem->getAbsolutePath(
            $moduleRelativePath
        );

        if (file_exists($moduleAbsolutePath . "/meta.json")) {
            $module = $libModuleParser->parseMetaJson(
                $moduleDirectory,
                $moduleRelativePath
            );

            $this->modules[$moduleDirectory] = $module;
            $valid = $this->validateModule($module);

            if ($valid) {
                $this->registerModule($module, $moduleRelativePath);
            }

            if (
                file_exists(
                    $this->customDirectory . "/" . $module->getName() . ".json"
                )
            ) {
                // TODO: Implement meta.json overrides
            }
        }
    }

    /**
     * Validates a module.
     *
     * @see vcms\module\LibModule
     *
     * @param module\LibModule $module A vcms\module\LibModule object.
     * @return bool
     */
    function validateModule(module\LibModule $module): bool
    {
        global $libGlobal, $libSecurityManager;

        $result = true;

        foreach ($module->pages as $page) {
            // does the page have a restriction?
            if ($page->hasAccessRestriction()) {
                $accessRestriction = $page->getAccessRestriction();

                //does the page have a function restriction?
                if ($accessRestriction->hasAemterRestriction()) {
                    $impossibleAemter = array_diff(
                        $accessRestriction->getAemter(),
                        $libSecurityManager->getPossibleAemter()
                    );

                    if (
                        is_array($impossibleAemter) &&
                        count($impossibleAemter) > 0
                    ) {
                        $libGlobal->errorTexts[] =
                            "Seite " .
                            $page->getPid() .
                            " in Modul " .
                            $module->name .
                            " hat eine Restriktion mit den folgenden nicht vorgesehenen Ämtern: " .
                            implode(", ", $impossibleAemter);
                        $result = false;
                    }
                }
            }
        }

        foreach ($module->includes as $include) {
            //does the include have a restriction?
            if ($include->hasAccessRestriction()) {
                $accessRestriction = $include->getAccessRestriction();

                // does the include haven a function restriction?
                if ($accessRestriction->hasAemterRestriction()) {
                    $impossibleAemter = array_diff(
                        $accessRestriction->getAemter(),
                        $libSecurityManager->getPossibleAemter()
                    );

                    if (
                        is_array($impossibleAemter) &&
                        count($impossibleAemter) > 0
                    ) {
                        $libGlobal->errorTexts[] =
                            "Include " .
                            $include->getPid() .
                            " in Modul " .
                            $module->name .
                            " hat eine Restriktion mit den folgenden nicht vorgesehenen Ämtern: " .
                            implode(", ", $impossibleAemter);
                        $result = false;
                    }
                }
            }
        }

        foreach ($module->pages as $page) {
            //check for colliding pid
            if (array_key_exists($page->getPid(), $this->pidToModulePointer)) {
                $result = false;
            }
        }

        foreach ($module->includes as $include) {
            //check for colliding iid
            if (
                array_key_exists($include->getIid(), $this->iidToModulePointer)
            ) {
                $result = false;
            }
        }

        foreach ($module->menuElementsInternet as $menuElement) {
            if (!$this->menuElementHasValidPid($menuElement, $module->pages)) {
                $libGlobal->errorTexts[] =
                    "Die Seiten-Id " .
                    $menuElement->getPid() .
                    " in Modul " .
                    $module->name .
                    " existiert nicht für eine Seite, ist aber in einem Menüeintrag angegeben.";
                $result = false;
            }
        }

        foreach ($module->menuElementsIntranet as $menuElement) {
            if (!$this->menuElementHasValidPid($menuElement, $module->pages)) {
                $libGlobal->errorTexts[] =
                    "Die Seiten-Id " .
                    $menuElement->getPid() .
                    " in Modul " .
                    $module->name .
                    " existiert nicht für eine Seite, ist aber in einem Menüeintrag angegeben.";
                $result = false;
            }
        }

        foreach ($module->menuElementsAdministration as $menuElement) {
            if (!$this->menuElementHasValidPid($menuElement, $module->pages)) {
                $libGlobal->errorTexts[] =
                    "Die Seiten-Id " .
                    $menuElement->getPid() .
                    " in Modul " .
                    $module->name .
                    " existiert nicht für eine Seite, ist aber in einem Menüeintrag angegeben.";
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Registers a module.
     *
     * @param module\LibModule $module A vcms\module\LibModule object.
     * @param string $moduleRelativePath
     */
    function registerModule(
        module\LibModule $module,
        string $moduleRelativePath
    ): void {
        foreach ($module->pages as $page) {
            $page->setDirectory(
                $moduleRelativePath . "/" . $page->getDirectory()
            );
        }

        foreach ($module->includes as $include) {
            $include->setDirectory(
                $moduleRelativePath . "/" . $include->getDirectory()
            );
        }

        foreach ($module->pages as $page) {
            $this->pages[$page->getPid()] = $page;
        }

        foreach ($module->includes as $include) {
            $this->includes[$include->getIid()] = $include;
        }

        foreach ($module->pages as $page) {
            $this->pidToModulePointer[$page->getPid()] = $module;
        }

        foreach ($module->includes as $include) {
            $this->iidToModulePointer[$include->getIid()] = $module;
        }

        foreach ($module->menuElementsInternet as $menuElement) {
            $this->menuElementAddAccessRestriction(
                $menuElement,
                $module->pages
            );
            $this->menuInternet->addMenuElement($menuElement);
        }

        foreach ($module->menuElementsIntranet as $menuElement) {
            $this->menuElementAddAccessRestriction(
                $menuElement,
                $module->pages
            );
            $this->menuIntranet->addMenuElement($menuElement);
        }

        foreach ($module->menuElementsAdministration as $menuElement) {
            $this->menuElementAddAccessRestriction(
                $menuElement,
                $module->pages
            );
            $this->menuAdministration->addMenuElement($menuElement);
        }
    }

    /**
     * Checks if a menu element has a valid pid.
     *
     * @param object $menuElement A vcms\menu\LibMenuElement object.
     * @param array $pages An array of vcms\module\LibPage objects.
     * @return bool
     */
    function menuElementHasValidPid(
        menu\LibMenuElement $menuElement,
        array $pages
    ): bool {
        if (
            $menuElement->getPid() != "" &&
            ($menuElement->getType() == 1 || $menuElement->getType() == 2)
        ) {
            $pidPresent = false;

            foreach ($pages as $page) {
                if ($page->getPid() == $menuElement->getPid()) {
                    $pidPresent = true;
                }
            }
        } else {
            $pidPresent = true;
        }

        return $pidPresent;
    }

    /**
     * Adds access restrictions to a menu element.
     *
     * @param menu\LibMenuElement $menuElement
     * @param array $pages An array of vcms\module\LibPage objects.
     */
    function menuElementAddAccessRestriction(
        menu\LibMenuElement $menuElement,
        array $pages
    ): void {
        global $libGlobal;

        //for all menu entries except external links
        if ($menuElement->getType() != 3) {
            if ($menuElement->getPid() != "") {
                $pageFound = false;

                foreach ($pages as $page) {
                    //select the page for the pid
                    if ($page->getPid() == $menuElement->getPid()) {
                        $menuElement->setAccessRestriction(
                            $page->getAccessRestriction()
                        );
                        $pageFound = true;
                    }
                }

                if (!$pageFound) {
                    $libGlobal->errorTexts[] =
                        "Für das Menüelement " .
                        $menuElement->getPid() .
                        " existiert keine Seite.";
                }
            }
        }

        //a menu folder?
        if (
            $menuElement->getType() == 2 &&
            $menuElement instanceof menu\LibMenuFolder
        ) {
            $elements = $menuElement->getElements();

            for ($i = 0; $i < count($elements); $i++) {
                $subMenuElement = $elements[$i];
                $this->menuElementAddAccessRestriction($subMenuElement, $pages);
            }
        }
    }

    /**
     * Returns a page by its pid.
     *
     * @param string $pid
     * @return object
     */
    function getPage(string $pid): object
    {
        global $libGlobal;

        if (!array_key_exists($pid, $this->pidToModulePointer)) {
            $libGlobal->errorTexts[] =
                "Angeforderte Page-Id " . $pid . " unbekannt.";
        } else {
            $pages = $this->pidToModulePointer[$pid]->getPages();
            return $pages[$pid];
        }
    }

    /**
     * Checks if a page exists.
     *
     * @param string $pid
     * @return bool
     */
    function pageExists(string $pid): bool
    {
        return array_key_exists($pid, $this->pidToModulePointer);
    }

    /**
     * Returns an include by its iid.
     *
     * @param string $iid
     * @return object
     */
    function getInclude(string $iid): object
    {
        global $libGlobal;

        if (!array_key_exists($iid, $this->iidToModulePointer)) {
            $libGlobal->errorTexts[] =
                "Angeforderte Include-Id " . $iid . " unbekannt.";
        } else {
            $includes = $this->iidToModulePointer[$iid]->getIncludes();
            return $includes[$iid];
        }
    }

    /**
     * Checks if an include exists.
     *
     * @param string $iid
     * @return bool
     */
    function includeExists(string $iid): bool
    {
        return array_key_exists($iid, $this->iidToModulePointer);
    }

    /**
     * Returns a module by its module id.
     *
     * @param string $moduleid
     * @return object
     */
    function getModuleByPageid(string $pid): object
    {
        global $libGlobal;

        if (!array_key_exists($pid, $this->pidToModulePointer)) {
            $libGlobal->errorTexts[] =
                "Angeforderte Page-Id " . $pid . " unbekannt.";
        } else {
            return $this->pidToModulePointer[$pid];
        }
    }

    /**
     * Returns a module by its include id.
     *
     * @param string $iid
     * @return module\LibModule
     */
    function getModuleByIncludeid($iid): module\LibModule
    {
        global $libGlobal;

        if (!array_key_exists($iid, $this->iidToModulePointer)) {
            $libGlobal->errorTexts[] =
                "Angeforderte Include-Id " . $iid . " unbekannt.";
        } else {
            return $this->iidToModulePointer[$iid];
        }
    }

    /**
     * Returns a module by its module id.
     *
     * @param string $moduleid
     * @return module\LibModule
     */
    function getModuleByModuleid($moduleid): module\LibModule
    {
        global $libGlobal;

        if (!array_key_exists($moduleid, $this->modules)) {
            $libGlobal->errorTexts[] =
                "Angeforderte Modul-Id " . $moduleid . " unbekannt.";
        } else {
            return $this->modules[$moduleid];
        }
    }

    /**
     * Tries to get a module by the current pid or iid. Prints an error message if no module is found.
     *
     * @return module\LibModule
     */
    function getModule(): module\LibModule
    {
        global $libGlobal;

        if ($libGlobal->pid != "") {
            return $this->getModuleByPageid($libGlobal->pid);
        } elseif ($libGlobal->iid != "") {
            return $this->getModuleByIncludeid($libGlobal->iid);
        } else {
            $libGlobal->errorTexts[] =
                'Weder $libGlobal->pid noch $libGlobal->iid sind mit einem Wert belegt';
        }
    }

    /**
     * Returns the directory of a module by its page id.
     *
     * @param string $pid
     * @return string
     */
    function getModuleDirectoryByPageid(string $pid): string
    {
        $module = $this->getModuleByPageid($pid);
        return $module->getPath();
    }

    /**
     * Returns the directory of a module by its include id.
     *
     * @param string $iid
     * @return string
     */
    function getModuleDirectoryByIncludeid(string $iid): string
    {
        $module = $this->getModuleByIncludeid($iid);
        return $module->getPath();
    }

    /**
     * Returns the directory of a module.
     *
     * @return string
     */
    function getModuleDirectoryByModuleid(string $moduleid): string
    {
        $module = $this->getModuleByModuleid($moduleid);
        return $module->getPath();
    }

    /**
     * Returns the directory of a module by the current pid or iid. Prints an error message if no module is found.
     *
     * @return string
     */
    function getModuleDirectory(): string
    {
        global $libGlobal;

        if ($libGlobal->pid != "") {
            return $this->getModuleDirectoryByPageid($libGlobal->pid);
        } elseif ($libGlobal->iid != "") {
            return $this->getModuleDirectoryByIncludeid($libGlobal->iid);
        } else {
            $libGlobal->errorTexts[] =
                'Weder $libGlobal->pid noch $libGlobal->iid sind mit einem Wert belegt';
        }
    }

    /**
     * Checks if a module is available.
     *
     * @param string $moduleId
     * @return bool
     */
    function moduleIsAvailable(string $moduleId): bool
    {
        return array_key_exists($moduleId, $this->modules);
    }

    /**
     * Returns an array of all modules.
     *
     * @return array
     */
    function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Returns an array of all pages.
     *
     * @return array
     */
    function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Returns an array of all includes.
     *
     * @return array
     */
    function getIncludes(): array
    {
        return $this->includes;
    }

    /**
     * Returns the publicly available menu items.
     *
     * @return menu\LibMenu
     */
    function getMenuInternet(): menu\LibMenu
    {
        $menu = $this->menuInternet;

        $menu->canonizeElements();
        $menu->sortElementsByPosition();
        $menu->applyMinAccessRestriction();

        return $menu;
    }

    /**
     * Returns the intranet menu items. Only available for logged in users.
     *
     * @return menu\LibMenu
     */
    function getMenuIntranet(): menu\LibMenu
    {
        $menu = $this->menuIntranet;

        $menu->canonizeElements();
        $menu->sortElementsByPosition();
        $menu->applyMinAccessRestriction();

        return $menu;
    }

    /**
     * Returns the administration menu items. Only available for users with administration rights.
     *
     * @return menu\LibMenu
     */
    function getMenuAdministration(): menu\LibMenu
    {
        $menu = $this->menuAdministration;

        $menu->canonizeElements();
        $menu->sortElementsByPosition();
        $menu->applyMinAccessRestriction();

        return $menu;
    }
}
