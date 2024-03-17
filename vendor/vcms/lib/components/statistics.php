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
    echo '<div class="row py-5 my-5">';

    echo "<h2 class='text-left'>$heading</h2>";
    if ($paragraph) {
        echo "<p class='text-left'>$paragraph</p>";
    }

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    renderStat($first);
    echo '</div>';

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    renderStat($second);
    echo '</div>';

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    renderStat($third);
    echo '</div>';


    echo '</div>';
}

function renderStat(Stat $stat): void
{
    $value = $stat->value;
    echo "<p class='display-2'>$value</p>";
    $hint = $stat->hint;
    echo "<p class=''>$hint</p>";
}

function renderStatisticsStrip(
    Stat $first,
    Stat $second,
    Stat $third,
    Stat $fourth
): void
{
    echo '<div class="row py-5 my-5 ">';

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    $h = $first->value;
    echo "<p class='h1'>$h</p>";
    $j = $first->hint;
    echo "<p class=''>$j</p>";
    echo '</div>';

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    $h = $second->value;
    echo "<p class='h1'>$h</p>";
    $j = $second->hint;
    echo "<p class=''>$j</p>";
    echo '</div>';

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    $h = $third->value;
    echo "<p class='h1'>$h</p>";
    $j = $third->hint;
    echo "<p class=''>$j</p>";
    echo '</div>';

    echo "<div class='col-xs-12 col-md-3 mt-4'>";
    $h = $fourth->value;
    echo "<p class='h1'>$h</p>";
    $j = $fourth->hint;
    echo "<p class=''>$j</p>";
    echo '</div>';


    echo '</div>';
}