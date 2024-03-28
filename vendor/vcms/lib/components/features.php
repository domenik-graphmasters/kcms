<?php

function renderFeaturesHangingIcons(
    string  $headline,
    ?string $paragraph,
    array   $entries
): void
{
    echo '<div class="row py-5 my-5">';

    echo "<h2 class='text-left'>$headline</h2>";
    if ($paragraph) {
        echo "<p class='text-left'>$paragraph</p>";
    }

    echo "<div class='col-xs-12 col-md-4 mt-4'>";
    hangingFeature($entries[0]['icon'], $entries[0]['headline'], $entries[0]['paragraph'], $entries[0]['buttonText'], $entries[0]['buttonUrl']);
    echo '</div>';

    echo "<div class='col-xs-12 col-md-4 mt-4'>";
    hangingFeature($entries[1]['icon'], $entries[1]['headline'], $entries[1]['paragraph'], $entries[1]['buttonText'], $entries[1]['buttonUrl']);
    echo '</div>';

    echo "<div class='col-xs-12 col-md-4 mt-4'>";
    hangingFeature($entries[2]['icon'], $entries[2]['headline'], $entries[2]['paragraph'], $entries[2]['buttonText'], $entries[2]['buttonUrl']);
    echo '</div>';

    echo '</div>';
}

function hangingFeature(
    string $iconId, string $headline, string $paragraph, string $buttonText, string $buttonUrl
): void
{
    echo "<div style='display: flex; justify-content: flex-start;'>";

    echo "<i class='fa fa-$iconId me-3 p-4 bg-info img-rounded mt-3' aria-hidden='true' style='display: inline-flex; justify-content: center; align-items: center; width: 3rem; height: 3rem;'></i>";
    echo "<div>";
    echo "<h3 class='text-left'>$headline</h3>";
    echo "<p class='text-left'>$paragraph</p>";
    echo "<a href='$buttonUrl'class='btn btn-primary mt-2'>$buttonText</a>";
    echo "</div>";

    echo "</div>";
}

function renderFeaturesCards(
    string  $headline,
    ?string $paragraph,
    array   $entries
): void
{
    echo '<div class="row py-5 my-5">';

    echo "<h2 class='text-left'>$headline</h2>";
    if ($paragraph) {
        echo "<p class='text-left'>$paragraph</p>";
    }

    echo "<div class='col-xs-12 col-md-4'>";
    card($entries[0]['icon'], $entries[0]['headline'], $entries[0]['paragraph'], $entries[0]['buttonText'], $entries[0]['buttonUrl']);
    echo '</div>';

    echo "<div class='col-xs-12 col-md-4'>";
    card($entries[1]['icon'], $entries[1]['headline'], $entries[1]['paragraph'], $entries[1]['buttonText'], $entries[1]['buttonUrl']);
    echo '</div>';

    echo "<div class='col-xs-12 col-md-4'>";
    card($entries[2]['icon'], $entries[2]['headline'], $entries[2]['paragraph'], $entries[2]['buttonText'], $entries[2]['buttonUrl']);
    echo '</div>';

    echo '</div>';
}

function card(string $iconId, string $headline, string $paragraph, string $buttonText, string $buttonUrl): void
{
    echo "<div class='panel panel-default'>";
    echo "<div class='panel-body text-center'>";
    echo "<i class='fa fa-$iconId p-4 bg-info img-rounded' aria-hidden='true'></i>";
    echo "<h3 class='text-center'>$headline</h3>";
    echo "<p class='text-center'>$paragraph</p>";
    echo "<a href='$buttonUrl'class='btn btn-primary'>$buttonText</a>";
    echo '</div>';
    echo '</div>';
}