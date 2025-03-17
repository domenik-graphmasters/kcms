<?php

function renderQuoteCarousel(?string $headline, array $entries): void
{
    global $libModuleHandler;
    echo '<div class="row">';
    if ($headline) {
        echo '<div class="col-xs-12 col-md-10">';
        echo "<h2 class='text-start'>$headline</h2>";
        echo "</div>";
    }

    echo '<div class="col-md-2 col-xs-12">';
    echo '<a class="btn btn-outline-primary m-4" href="#carousel-example-generic" role="button" data-bs-slide="prev"><</a>';
    echo '<a class="btn btn-outline-primary" href="#carousel-example-generic" role="button" data-bs-slide="next">></a>';
    echo "</div>";
    echo "</div>";

    echo '<div class="row">';

    echo '<div class="col-12">';
    echo '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">';
    echo '<div class="carousel-inner" role="listbox">';
    // TODO: Improve
    $isFirst = true;
    foreach ($entries as $entry) {
        carouselItem(
            $entry["headline"],
            $entry["paragraph"],
            $entry["imageUrl"],
            $isFirst
        );
        $isFirst = false;
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "</div>";
}

function carouselItem(
    string $headline,
    string $paragraph,
    string $imageUrl,
    bool $active = false
): void {
    echo '<div class="carousel-item';
    if ($active) {
        echo " active";
    }
    echo '">';

    echo '<div class="row">';
    echo '<div class="col-12 col-lg-6">';
    echo "<img src='$imageUrl' class='img-fluid'>";
    echo "</div>";
    echo '<div class="col-12 col-lg-6">';
    echo "<h3>$headline</h3>";
    echo "<p>$paragraph</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}
