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
    Stat $first,
    Stat $second,
    Stat $third,
): void
{
    echo '<div class="row">';
    echo "<div class='alert alert-warning' role='alert'>renderStatisticsWithExplainer is not implemented</div>";
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