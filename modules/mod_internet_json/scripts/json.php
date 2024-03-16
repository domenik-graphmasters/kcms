<?php

include_once "components/faq.php";
include_once "components/features.php";
include_once "components/gallery.php";
include_once "components/hero.php";
include_once "components/quote.php";
include_once "components/statistics.php";

$path = "modules/mod_internet_json/scripts/index.json";
$content = file_get_contents($path);
if (!$content) {
    echo "failed to read contents of " . $path;
    return;
}

$json = json_decode($content, associative: true);

if (json_last_error()) {
    echo json_last_error_msg();
    return;
}

foreach ($json as $item) {
    switch ($item['id']) {
        case 'hero-centered-small-image':
            renderHeroCenteredSmallImage(
                title: $item['title'],
                paragraph: $item['paragraph'],
                primaryButtonText: $item['primaryButtonText'],
                primaryButtonUrl: $item['primaryButtonUrl'],
                secondaryButtonText: $item['secondaryButtonText'],
                secondaryButtonUrl: $item['secondaryButtonUrl'],
                imageUrl: $item['imageUrl']
            );
            break;
        case 'hero-centered-large-image':
            renderHeroCenteredLargeImage(
                title: $item['title'],
                paragraph: $item['paragraph'],
                primaryButtonText: $item['primaryButtonText'],
                primaryButtonUrl: $item['primaryButtonUrl'],
                secondaryButtonText: $item['secondaryButtonText'],
                secondaryButtonUrl: $item['secondaryButtonUrl'],
                imageUrl: $item['imageUrl']
            );
            break;
        case 'hero-right-image':
            renderHeroRightImage(
                title: $item['title'],
                paragraph: $item['paragraph'],
                primaryButtonText: $item['primaryButtonText'],
                primaryButtonUrl: $item['primaryButtonUrl'],
                secondaryButtonText: $item['secondaryButtonText'],
                secondaryButtonUrl: $item['secondaryButtonUrl'],
                imageUrl: $item['imageUrl']
            );
            break;
        case 'hero-right-contact':
            renderHeroRightContact(
                title: $item['title'],
                paragraph: $item['paragraph']
            );
            break;

        case 'faq-accordion':
            renderFaqAccordion(
                title: $item['title'],
                paragraph: $item['paragraph'],
                entries: $item['entries']
            );
            break;

        case 'faq-accordion-image':
            renderFaqAccordionWithImage(
                title: $item['title'],
                paragraph: $item['paragraph'],
                entries: $item['entries'],
                imageUrl: $item['imageUrl']
            );
            break;
        default:
            echo '<div class="row">';
            echo 'id is not supported: ' . $item['id'] . "\n";
            echo '</div>';
    }
}