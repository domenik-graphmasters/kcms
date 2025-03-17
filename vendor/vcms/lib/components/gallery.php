<?php

namespace vcms\components;

function renderGallerySingleFluid(string $imageUrl): void
{
    echo '<div class="row py-5 my-5"">';

    echo "<img src=$imageUrl class='img-fluid rounded' />";

    echo "</div>";
}

function renderGallerySingleWithText(
    string $imageUrl,
    ?string $headline,
    string $paragraph
): void {
    echo '<div class="row py-5 my-5">';

    echo '<div class="col-xs-12 col-md-6 order-1">';
    echo "<img src=$imageUrl class='img-fluid rounded' />";
    echo "</div>";

    echo '<div class="col-xs-12 col-md-6 order-0">';
    echo "<h2 class='text-start'>$headline</h2>";
    echo "<p class='text-start'>$paragraph</p>";
    echo "</div>";

    echo "</div>";
}

function renderGalleryTriplet(
    string $prominentImageUrl,
    string $topImageUrl,
    string $bottomImageUrl
): void {
    echo '<div class="row py-5 my-5 img-gallery">';

    echo "<div class='col-xs-12 col-md-8 mt-4'>";
    echo "<img src=$prominentImageUrl class='rounded gallery-img-prominent' />";
    echo "</div>";

    echo "<div class='col-xs-12 col-md-4 mt-4 gallery-img-stack'>";
    echo "<img src=$topImageUrl class='rounded' />";
    echo "<img src=$bottomImageUrl class='rounded mt-4' />";
    echo "</div>";

    echo "</div>";
}

function renderGalleryQuintet(
    string $prominentImageUrl,
    string $topstartImageUrl,
    string $bottomstartImageUrl,
    string $topRightImageUrl,
    string $bottomRightImageUrl
): void {
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

function renderGalleryThreeEquals(
    string $headline,
    string $paragraph,
    ImageCardState $image1,
    ImageCardState $image2,
    ImageCardState $image3
): void {
    echo '<div class="row py-5 my-5">';

    echo '<div class="col-xs-12">';
    echo "<h2 class='mt-4 mb-3 text-start'>$headline</h2>";
    echo "<p class='text-start'>$paragraph</p>";
    echo "</div>";

    renderCardWithTopImage($image1);
    renderCardWithTopImage($image2);
    renderCardWithTopImage($image3);

    echo "</div>";
}

function renderCardWithTopImage(ImageCardState $state): void
{
    $imgUrl = $state->imageUrl;
    $title = $state->title;
    $paragraph = $state->paragraph;

    echo '<div class="col-12 col-md-4">';
    echo '<div class="card mb-3">';
    echo "<img src='$imgUrl' class='card-img-top' loading='lazy' >";
    echo '<div class="card-body">';
    if ($title) {
        echo "<h5 class='card-title'>$title</h5>";
    }

    if ($paragraph) {
        echo "<p class='card-text'>$paragraph</p>";
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

readonly class ImageCardState
{
    public function __construct(
        public ?string $title,
        public ?string $paragraph,
        public string $imageUrl
    ) {}
}
