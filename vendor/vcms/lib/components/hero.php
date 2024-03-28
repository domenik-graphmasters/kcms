<?php

function renderHeroCenteredSmallImage(
    string  $title,
    string  $paragraph,
    string  $primaryButtonText,
    string  $primaryButtonUrl,
    ?string $secondaryButtonText,
    ?string $secondaryButtonUrl,
    ?string $imageUrl
): void
{
    echo '<div class="row text-center py-5 my-5">';

    if ($imageUrl) {
        echo "<img src=$imageUrl class='mb-4' height='72' width='57' >";
    }
    echo "<h1 class='mt-4 mb-3'>$title</h1>";
    echo "<div class='col-md-8 col-md-offset-2'>";
    echo "<p class='lead'>$paragraph</p>";
    echo "<div class=''>";
    if ($secondaryButtonText) {
        echo "<a href='$primaryButtonUrl' class='btn btn-primary btn-lg mx-2'>$primaryButtonText</a>";
        echo "<a href='$secondaryButtonUrl' class='btn btn-default btn-lg mx-2'>$secondaryButtonText</a>";
    } else {
        echo "<a href='$primaryButtonUrl' class='btn btn-primary btn-lg'>$primaryButtonText</a>";

    }
    echo "</div>";
    echo "</div>";

    echo '</div>';
}

function renderHeroCenteredLargeImage(
    string  $title,
    string  $paragraph,
    string  $primaryButtonText,
    string  $primaryButtonUrl,
    ?string $secondaryButtonText,
    ?string $secondaryButtonUrl,
    ?string $imageUrl
): void
{
    echo '<div class="row text-center py-5 my-5">';
    echo "<h1 class='mt-4 mb-3'>$title</h1>";
    echo "<div class='col-md-8 col-md-offset-2'>";
    echo "<p class='lead'>$paragraph</p>";
    echo "<div class='mb-5'>";
    if ($secondaryButtonText) {
        echo "<a href='$primaryButtonUrl' class='btn btn-primary btn-lg mx-2'>$primaryButtonText</a>";
        echo "<a href='$secondaryButtonUrl' class='btn btn-default btn-lg mx-2'>$secondaryButtonText</a>";
    } else {
        echo "<a href='$primaryButtonUrl' class='btn btn-primary btn-lg'>$primaryButtonText</a>";

    }
    echo "</div>";


    if ($imageUrl) {
        echo "<div class='px-5 center-block' style='max-height: 30vh; overflow: clip'>";
        echo "<img src=$imageUrl class='mb-4 img-responsive center-block shadow-lg' loading='lazy' width='700' height='500'>";
        echo "</div>";
    }

    echo "</div>";

    echo '</div>';
}

function renderHeroRightImage(
    string  $title,
    string  $paragraph,
    string  $primaryButtonText,
    string  $primaryButtonUrl,
    ?string $secondaryButtonText,
    ?string $secondaryButtonUrl,
    ?string $imageUrl
): void
{
    echo '<div class="row py-5 my-5">';
    echo "<div class='col-xs-12 col-md-6 col-md-push-6'>";
    echo "<img src=$imageUrl class='mb-4 img-responsive center-block' loading='lazy' width='700' height='500'>";
    echo '</div>';
    echo "<div class='col-sx-12 col-md-6 col-md-pull-6'>";
    echo "<h1 class='mt-4 mb-3 text-left'>$title</h1>";
    echo "<div class=''>";
    echo "<p class='lead text-left'>$paragraph</p>";
    echo "<div class='mb-5'>";
    if ($secondaryButtonText) {
        echo "<a href='$primaryButtonUrl' class='btn btn-primary btn-lg'>$primaryButtonText</a>";
        echo "<a href='$secondaryButtonUrl' class='btn btn-default btn-lg mx-3'>$secondaryButtonText</a>";
    } else {
        echo "<a href='$primaryButtonUrl' class='btn btn-primary btn-lg'>$primaryButtonText</a>";
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

}

function renderHeroRightContact(
    string $title,
    string $paragraph,
): void
{
    echo '<div class="row py-5 my-5">';
    echo "<div class='col-sx-12 col-md-6'>";
    echo "<h1 class='mt-4 mb-3 text-left'>$title</h1>";
    echo "<div class=''>";
    echo "<p class='lead text-left'>$paragraph</p>";
    echo '</div>';
    echo '</div>';
    echo "<div class='col-xs-12 col-md-6'>";
    // TODO: Contact us form

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

function formGroup(string $id, string $label, callable $input, string $inputClass = ""): void
{
    echo "<div class='form-group'>";
    echo "<label for=$id class='col-sm-3 control-label'>$label</label>";
    echo "<div class='col-sm-9 $inputClass'>";
    echo $input($id);
    echo "</div>";
    echo "</div>";
}

function formGroupInput(string $id, string $label, string $type): void
{
    formGroup(id: $id, label: $label, input: function (string $id) use ($type) {
        return "<input type=$type id=$id name=$id value='' required='' class='form-control'>";
    });
}