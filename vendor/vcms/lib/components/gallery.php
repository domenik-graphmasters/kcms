<?php

function renderGallerySingleFluid(
    string $imageUrl
): void
{
    echo '<div class="row py-5 my-5"">';

    echo "<img src=$imageUrl class='img-responsive img-rounded' />";

    echo '</div>';
}

function renderGallerySingleWithText(
    string  $imageUrl,
    ?string $headline,
    string  $paragraph
): void
{
    echo '<div class="row py-5 my-5">';

    echo '<div class="col-xs-12 col-md-6 col-md-push-6">';
    echo "<img src=$imageUrl class='img-responsive img-rounded' />";
    echo '</div>';

    echo '<div class="col-xs-12 col-md-6 col-md-pull-6">';
    echo "<h2 class='text-left'>$headline</h2>";
    echo "<p class='text-left'>$paragraph</p>";
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
    echo "<img src=$prominentImageUrl class='img-rounded gallery-img-prominent' />";
    echo "</div>";

    echo "<div class='col-xs-12 col-md-4 mt-4 gallery-img-stack'>";
    echo "<img src=$topImageUrl class='img-rounded' />";
    echo "<img src=$bottomImageUrl class='img-rounded mt-4' />";
    echo '</div>';

    echo '</div>';
}

function renderGalleryQuintet(
    string $prominentImageUrl,
    string $topLeftImageUrl,
    string $bottomLeftImageUrl,
    string $topRightImageUrl,
    string $bottomRightImageUrl
): void
{
    echo '<div class="row py-5 my-5">';

    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$prominentImageUrl class='img-responsive img-rounded' style='width: 100%;' />";
    echo "</div>";
    echo "<div class='col-xs-12 col-md-6'>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$topLeftImageUrl class='img-responsive img-rounded' style='width: 100%;' />";
    echo "</div>";

    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$topRightImageUrl class='img-responsive img-rounded' style='width: 100%;' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$bottomLeftImageUrl class='img-responsive img-rounded' style='width: 100%;' />";
    echo "</div>";
    echo "<div class='col-xs-12 col-md-6 mt-4'>";
    echo "<img src=$bottomRightImageUrl class='img-responsive img-rounded' style='width: 100%;' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "</div>";
}