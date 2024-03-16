<?php

readonly class Stat
{
    public function __construct(
        public string $value,
        public string $hint,
    )
    {
    }
}

function renderStatisticsWithExplainer(
    string $heading,
    string $paragraph,
    Stat $first,
    Stat $second,
    Stat $third,
): void
{
    echo '<div class="row px-4 py-5 my-5">';

    echo "<h2 class='text-left'>$heading</h2>";
    if ($paragraph) {
        echo "<p class='text-left'>$paragraph</p>";
    }

    echo "<div class='col-xs-12 col-md-4 mt-4'>";
    echo '</div>';


    echo '</div>';
}

function renderStatisticsStrip(
    Stat $first,
    Stat $second,
    Stat $third,
    Stat $fourth
): void
{
    echo '<div class="row">';
    echo "<div class='alert alert-warning' role='alert'>renderStatisticsStrip is not implemented</div>";
    echo '</div>';
}