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

        case 'features-hanging-icons':
            $entries = $item['entries'];
            if (sizeof($entries) != 3) {
                echo "<div class='alert alert-danger' role='alert'>features-hanging-icons benötigt genau 3 entries</div>";
                break;
            }
            renderFeaturesHangingIcons(
                headline: $item['headline'],
                paragraph: $item['paragraph'],
                entries: $entries
            );
            break;

        case 'features-cards':
            $entries = $item['entries'];
            if (sizeof($entries) != 3) {
                echo "<div class='alert alert-danger' role='alert'>features-cards benötigt genau 3 entries</div>";
                break;
            }
            renderFeaturesCards(
                headline: $item['headline'],
                paragraph: $item['paragraph'],
                entries: $entries
            );
            break;

        case 'gallery-single-fluid':
            renderGallerySingleFluid(imageUrl: $item['imageUrl']);
            break;

        case 'gallery-single-with-text':
            renderGallerySingleWithText(
                imageUrl: $item['imageUrl'],
                headline: $item['headline'],
                paragraph: $item['paragraph']
            );
            break;

        case 'gallery-triplet':
            renderGalleryTriplet(
                prominentImageUrl: $item['prominentImageUrl'],
                topImageUrl: $item['topImageUrl'],
                bottomImageUrl: $item['bottomImageUrl']
            );
            break;

        case 'gallery-quintet':
            renderGalleryQuintet(
                prominentImageUrl: $item['prominentImageUrl'],
                topLeftImageUrl: $item['topImageUrl'] ?? "",
                bottomLeftImageUrl: $item['bottomImageUrl'] ?? "",
                topRightImageUrl: $item['topImageUrl'] ?? "",
                bottomRightImageUrl: $item['bottomImageUrl'] ?? ""
            );
            break;

        case 'statistics-with-explainer':
            renderStatisticsWithExplainer(
                first: new Stat(
                    value: $item['firstStat']['value'],
                    hint: $item['firstStat']['hint']
                ),
                second: new Stat(
                    value: $item['secondStat']['value'],
                    hint: $item['secondStat']['hint']
                ),
                third: new Stat(
                    value: $item['thirdStat']['value'],
                    hint: $item['thirdStat']['hint']
                )
            );
            break;

        case 'statistics-strip':
            renderStatisticsStrip(
                first: new Stat(
                    value: $item['firstStat']['value'],
                    hint: $item['firstStat']['hint']
                ),
                second: new Stat(
                    value: $item['secondStat']['value'],
                    hint: $item['secondStat']['hint']
                ),
                third: new Stat(
                    value: $item['thirdStat']['value'],
                    hint: $item['thirdStat']['hint']
                ),
                fourth: new Stat(
                    value: $item['fourthStat']['value'],
                    hint: $item['fourthStat']['hint']
                )
            );
            break;

        case 'quote-carousel':
            renderQuoteCarousel(
                headline: $item['headline'],
                entries: $item['entries']
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
            $id = $item['id'];
            $itemText = json_encode($item);
            echo "<div class='alert alert-danger' role='alert'>id is not supported: $id; $itemText</div>";
            echo '</div>';
    }
}