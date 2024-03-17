<?php

namespace vcms;
include_once "components/faq.php";
include_once "components/features.php";
include_once "components/gallery.php";
include_once "components/hero.php";
include_once "components/quote.php";
include_once "components/statistics.php";

class LibComponentRenderer
{

    public function __construct()
    {
    }

    public function render(array $json): void
    {
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
                        topLeftImageUrl: $item['topLeftImageUrl'] ?? "",
                        bottomLeftImageUrl: $item['bottomLeftImageUrl'] ?? "",
                        topRightImageUrl: $item['topRightImageUrl'] ?? "",
                        bottomRightImageUrl: $item['bottomRightImageUrl'] ?? ""
                    );
                    break;

                case 'statistics-with-explainer':
                    renderStatisticsWithExplainer(
                        heading: $item['headline'],
                        paragraph: $item['paragraph'],
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

                case 'features-checkmark-list':
                    $this->renderFeaturesCheckmarkList(
                        headline: $item['headline'],
                        paragraph: $item['paragraph'],
                        entries: $item['entries'],
                        buttonText: $item['buttonText'],
                        buttonUrl: $item['buttonUrl'],
                        imageUrl: $item['imageUrl']
                    );
                    break;

                case 'timeline':
                    $this->renderTimeline(
                        headline: $item['headline'],
                        paragraph: $item['paragraph'],
                        entries: $item['entries'],
                        imageUrl: $item['imageUrl']
                    );
                    break;

                case 'basic-text-body':
                    $this->renderBasicTextBody(
                        headline: $item['headline'],
                        paragraph: $item['paragraph'],
                    );
                    break;

                case 'contact-us':
                    $this->renderContactForm(
                        headline: $item['headline'],
                        paragraph: $item['paragraph']
                    );
                    break;

                case 'coming-events':
                    $this->renderComingEvents(
                        headline: $item['headline'],
                        paragraph: $item['paragraph'],
                        eventCount: $item['eventCount']
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
    }

    private function renderFeaturesCheckmarkList(
        string  $headline,
        string  $paragraph,
        array   $entries,
        ?string $buttonText,
        ?string $buttonUrl,
        string  $imageUrl,
    ): void
    {
        echo '<div class="row py-5 my-5">';
        echo '<div class="col-xs-12 col-md-6">';
        echo "<h2 class='mt-4 mb-3 text-left'>$headline</h2>";
        echo "<p class='text-left'>$paragraph</p>";
        if ($buttonText && $buttonUrl) {
            echo "<a href=$buttonUrl class='btn btn-default mt-2'>$buttonText</a>";
        }

        echo '<div class="my-4">';
        foreach ($entries as $entry) {
            echo "<p><i class='fa fa-check m-4' aria-hidden='true'></i>$entry</p>";
        }

        echo '</div>';
        echo '</div>';
        echo '<div class="col-xs-12 col-md-6">';
        echo "<img src=$imageUrl class='img-responsive img-rounded' />";
        echo '</div>';
        echo '</div>';
    }

    private function renderTimeline(
        string $headline,
        string $paragraph,
        array  $entries,
        string $imageUrl
    ): void
    {
        echo '<div class="row pt-5 mt-5">';
        echo '<div class="col-xs-12">';
        echo "<h2 class='mt-4 mb-3 text-left'>$headline</h2>";
        echo '</div>';
        echo '</div>';

        echo '<div class="row pb-5 mb-5">';
        echo '<div class="col-xs-12 col-md-6 col-md-push-6">';
        echo "<img src=$imageUrl class='img-responsive img-rounded' />";
        echo '</div>';
        echo '<div class="col-xs-12 col-md-6 col-md-pull-6">';
        echo "<p class='text-left'>$paragraph</p>";

        foreach ($entries as $entry) {
            $entryHeadline = $entry['headline'];
            $entryParagraph = $entry['paragraph'];
            echo "<h3 class='mt-5 mb-2 text-left'>$entryHeadline</h3>";
            echo "<p class='text-left'>$entryParagraph</p>";
        }

        echo '</div>';
        echo '</div>';
    }

    private function renderBasicTextBody(
        string $headline,
        string $paragraph
    ): void
    {
        echo '<div class="row py-5 my-5">';

        echo '<div class="col-xs-12">';
        echo "<h2 class='mt-4 mb-3 text-left'>$headline</h2>";
        echo "<p class='text-left'>$paragraph</p>";
        echo '</div>';

        echo '</div>';
    }

    private function renderContactForm(
        string $headline,
        string $paragraph
    ): void
    {
        echo '<div class="row py-5 my-5">';

        echo '<div class="col-xs-12 col-md-5">';
        echo "<h2 class='mt-4 mb-3 text-left'>$headline</h2>";
        echo "<p class='text-left'>$paragraph</p>";
        echo '</div>';

        echo '<div class="col-xs-12 col-md-7">';
        echo "<div class='panel panel-default'>";
        echo "<div class='panel-body'>";
        echo "<form action='index.php?pid=kontakt' method='post' class='form-horizontal'>";
        echo "<fieldset>";
        formGroupInput(id: "name", label: "Name", type: "text");
        formGroupInput(id: "emailaddress", label: "E-Mail-Adresse", type: "email");
        formGroupInput(id: "telefon", label: "Telefonnummer", type: "tel");
        formGroup(id: "nachricht", label: "Nachricht", input: function (string $id) {
            return "<textarea id=$id name=$id rows='10' required='' class='form-control'></textarea>";
        });
        formGroup(id: "", label: "", input: function (string $id) {
            return "<button type='submit' class='btn btn-primary btn-lg'><i class='fa fa-envelope-o' aria-hidden='true'></i> Abschicken</button>";
        }, inputClass: "col-sm-offset-3");
        echo "</div>";
        echo "</div>";
        echo '</div>';

        echo '</div>';
    }

    private function renderComingEvents(
        string $headline,
        string $paragraph,
        int    $eventCount
    ): void
    {
        echo '<div class="row py-5 my-5">';

        echo '<div class="col-xs-12">';
        echo "<h2 class='mt-4 mb-3 text-left'>$headline</h2>";
        echo '</div>';

        echo '<div class="col-xs-12 col-md-6">';
        echo "<p class='mt-4 mb-3 text-left'>$paragraph</p>";
        echo '</div>';

        echo '<div class="col-xs-12 col-md-6">';
        echo "<h3 class='mt-0 mb-3 text-left mb-5'>Veranstaltungen</h3>";

        // TODO(enhancement): Fetch actual events
        echo "<h4 class='mt-4 mb-3 text-left'>Nikolauskneipe</h4>";
        echo "<p class='text-left'>Kurz vor der Abreise in die Heimat wollen wir noch einmal einen festlichen Abend genießen</p>";
        echo "<p class='text-muted text-left'><i class='fa fa-clock-o m-2'></i>6.12.2023 um 20 c.t.</p>";

        echo "<h4 class='mt-4 mb-3 text-left'>Nikolauskneipe</h4>";
        echo "<p class='text-left'>Kurz vor der Abreise in die Heimat wollen wir noch einmal einen festlichen Abend genießen</p>";
        echo "<p class='text-left text-muted'><i class='fa fa-clock-o m-2'></i>6.12.2023 um 20 c.t.</p>";

        echo '</div>';

        echo '</div>';
    }
}