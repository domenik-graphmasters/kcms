<?php

function renderGallerySingleFluid(
    string $imageUrl
): void
{
    echo '<div class="row py-5 my-5"">';

    echo "<img src=$imageUrl class='img-fluid rounded' />";

    echo '</div>';
}

function renderGallerySingleWithText(
    string  $imageUrl,
    ?string $headline,
    string  $paragraph
): void
{
    echo '<div class="row py-5 my-5">';

    echo '<div class="col-xs-12 col-md-6 order-1">';
    echo "<img src=$imageUrl class='img-fluid rounded' />";
    echo '</div>';

    echo '<div class="col-xs-12 col-md-6 order-0">';
    echo "<h2 class='text-start'>$headline</h2>";
    echo "<p class='text-start'>$paragraph</p>";
    echo '</div>';

    echo '</div>';
}

function renderGalleryTriplet(
    string $prominentImageUrl,
    string $topImageUrl,
    string $bottomImageUrl
): void
{
    echo '<div class="row py-5 my-5 img-gallery">';

    echo "<div class='col-xs-12 col-md-8 mt-4'>";
    echo "<img src=$prominentImageUrl class='rounded gallery-img-prominent' />";
    echo "</div>";

    echo "<div class='col-xs-12 col-md-4 mt-4 gallery-img-stack'>";
    echo "<img src=$topImageUrl class='rounded' />";
    echo "<img src=$bottomImageUrl class='rounded mt-4' />";
    echo '</div>';

    echo '</div>';
}

function renderGalleryQuintet(
    string $prominentImageUrl,
    string $topstartImageUrl,
    string $bottomstartImageUrl,
    string $topRightImageUrl,
    string $bottomRightImageUrl
): void
{
    echo '<div class="row py-5 my-5">';

    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$prominentImageUrl class='img-fluid rounded' style='width: 100%;' />";
    echo "</div>";
    echo "<div class='col-xs-12 col-md-6'>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$topstartImageUrl class='img-fluid rounded' style='width: 100%;' />";
    echo "</div>";

    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$topRightImageUrl class='img-fluid rounded' style='width: 100%;' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$bottomstartImageUrl class='img-fluid rounded' style='width: 100%;' />";
    echo "</div>";
    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$bottomRightImageUrl class='img-fluid rounded' style='width: 100%;' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "</div>";
}