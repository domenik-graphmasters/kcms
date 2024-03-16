<?php

function renderFaqAccordion(
    string $title,
    string $paragraph,
    array  $entries
): void
{
    echo '<div class="row text-center px-4 py-5 my-5">';

    echo "<h2 class='mt-4 mb-3 text-left'>$title</h2>";
    echo "<p class='text-left'>$paragraph</p>";

    echo '<div class="col-xs-12 col-md-10 col-md-offset-1">';
    foreach ($entries as $entry) {
        $id = uniqid(prefix: 'faq-');
        $question = $entry['question'];
        echo "<a data-toggle='collapse' href='#$id' aria-expanded='false' aria-controls='$id'><h3 class='py-3 text-left col-xs-10'>$question</h3><i class='col-xs-2 fa fa-plus my-4 py-3' aria-hidden='true'></i></a>";
        echo "<div class='collapse' id=$id>";
        echo '<p class="text-left mx-3">' . $entry['answer'] . '</p>';
        echo '</div>';
        echo '<hr>';
    }
    echo '</div>';
    echo '</div>';
}


// TODO: Take image from entries
function renderFaqAccordionWithImage(
    string $title,
    string $paragraph,
    array  $entries,
    string $imageUrl
): void
{
    echo '<div class="row text-center px-4 py-5 my-5">';

    echo "<h2 class='mt-4 mb-3 text-left'>$title</h2>";
    echo "<p class='text-left'>$paragraph</p>";

    echo '<div class="col-xs-12 col-md-6 mb-5">';
    foreach ($entries as $index => $entry) {
        $question = $entry['question'];
        echo "<a data-toggle='collapse' href='#faq-$index' aria-expanded='false' aria-controls='faq-$index'><h3 class='py-3 text-left col-xs-10'>$question</h3><i class='col-xs-2 fa fa-plus my-4 py-3' aria-hidden='true'></i></a>";
        echo "<div class='collapse' id=faq-$index>";
        echo '<p class="text-left mx-3">' . $entry['answer'] . '</p>';
        echo '</div>';
        echo '<hr>';
    }
    echo '</div>';

    echo '<div class="col-xs-12 col-md-6">';
    echo "<img src=$imageUrl class='img-responsive img-rounded' width='700' height='700'/>";
    echo '</div>';
    echo '</div>';
}