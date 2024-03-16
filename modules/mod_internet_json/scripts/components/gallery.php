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
    echo '<div class="row px-4 py-5 my-5"">';

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
    echo '<div class="row">';
    echo "<div class='alert alert-warning' role='alert'>renderGalleryTriplet is not implemented</div>";
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
    echo '<div class="row">';
    echo "<div class='alert alert-warning' role='alert'>renderGalleryQuintet is not implemented</div>";
    echo '</div>';
}