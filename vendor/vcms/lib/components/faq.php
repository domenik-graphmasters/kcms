<?php

// TODO(bug): Questions with line break make influence paragraph format
function renderFaqAccordion(
    string $title,
    string $paragraph,
    array  $entries
): void
{
    echo '<div class="row text-center py-5 my-5">';

    echo "<h2 class='mt-4 mb-3 text-start'>$title</h2>";
    echo "<p class='text-start'>$paragraph</p>";

    echo '<div class="col-xs-12 col-md-10 col-md-offset-1">';
    foreach ($entries as $entry) {
        $id = uniqid(prefix: 'faq-');
        $question = $entry['question'];
        echo "<a data-bs-toggle='collapse' href='#$id' aria-expanded='false' aria-controls='$id' class='d-flex justify-content-between'><h3 class='py-3 text-start ms-4'>$question</h3><i class='fa fa-plus my-4 py-3 me-4' aria-hidden='true'></i></a>";
        echo "<div class='collapse' id=$id>";
        echo '<p class="text-start mx-4">' . $entry['answer'] . '</p>';
        echo '</div>';
        echo '<hr>';
    }
    echo '</div>';
    echo '</div>';
}


// TODO(enhancement): Take image from entries
function renderFaqAccordionWithImage(
    string $title,
    string $paragraph,
    array  $entries,
    string $imageUrl
): void
{
    echo '<div class="row text-center py-5 my-5">';

    echo "<h2 class='mt-4 mb-3 text-start'>$title</h2>";
    echo "<p class='text-start'>$paragraph</p>";

    echo '<div class="col-xs-12 col-md-6 mb-5">';
    foreach ($entries as $index => $entry) {
        $question = $entry['question'];
        echo "<a data-toggle='collapse' href='#faq-$index' aria-expanded='false' aria-controls='faq-$index'><h3 class='py-3 text-start col-xs-10'>$question</h3><i class='col-xs-2 fa fa-plus my-4 py-3' aria-hidden='true'></i></a>";
        echo "<div class='collapse' id=faq-$index>";
        echo '<p class="text-start mx-3">' . $entry['answer'] . '</p>';
        echo '</div>';
        echo '<hr>';
    }
    echo '</div>';

    echo '<div class="col-xs-12 col-md-6">';
    echo "<img src=$imageUrl class='img-fluid rounded' width='700' height='700'/>";
    echo '</div>';
    echo '</div>';
}