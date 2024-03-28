<?php

function renderQuoteCarousel(
    ?string $headline,
    array   $entries
): void
{
    global $libModuleHandler;
    echo '<div class="row">';
    if ($headline) {
        echo '<div class="col-xs-12 col-md-10">';
        echo "<h2 class='text-left'>$headline</h2>";
        echo "</div>";
    }

    echo '<div class="col-md-2 col-xs-12">';
    echo '<a class="btn btn-default m-4" href="#carousel-example-generic" role="button" data-slide="prev"><</a>';
    echo '<a class="btn btn-default" href="#carousel-example-generic" role="button" data-slide="next">></a>';
    echo '</div>';
    echo '</div>';

    echo '<div class="row">';

    echo '<div class="col-xs-12 col-md-4">';

    echo '</div>';
    echo '<div class="col-xs-12 col-md-8">';
    echo '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">';
    echo '<div class="carousel-inner" role="listbox">';
    // TODO: Improve
    $isFirst = true;
    foreach ($entries as $entry) {
        carouselItem($entry['headline'], $entry['paragraph'], $isFirst);
        $isFirst = false;
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';


    echo '</div>';
}

function carouselItem(string $headline, string $paragraph, bool $active = false): void
{
    echo '<div class="item';
    if ($active) echo ' active';
    echo '">';
    echo "<h3>$headline</h3>";
    echo "<p>$paragraph</p>";
    echo '</div>';
}