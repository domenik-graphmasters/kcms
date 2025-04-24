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

echo PHP_EOL;

global $libTemplateRenderer, $libGlobal, $libGenericStorage;

if ($libGlobal->page->isContainerEnabled()) {
    echo "      </div>" . PHP_EOL;
    echo "    </main>" . PHP_EOL;
}

$facebookUrl = $libGenericStorage->loadValue(
    "mod_internet_home",
    "facebook_url"
);
$instagramUrl = $libGenericStorage->loadValue(
    "mod_internet_home",
    "instagram_url"
);
$twitterUrl = $libGenericStorage->loadValue("mod_internet_home", "twitter_url");
$wikipediaUrl = $libGenericStorage->loadValue(
    "mod_internet_home",
    "wikipedia_url"
);

$libTemplateRenderer->display("footer.html.twig", [
    "facebookUrl" => $facebookUrl,
    "instagramUrl" => $instagramUrl,
    "twitterUrl" => $twitterUrl,
    "wikipediaUrl" => $wikipediaUrl,
    "autoupdate" => $libGenericStorage->loadValue("base_core", "auto_update"),
]);

echo "</body>" . PHP_EOL;
echo "</html>" . PHP_EOL;
