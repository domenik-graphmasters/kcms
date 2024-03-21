$(document).ready(function() {
	$.get("vendor/vcms/styles/gallery/modal.html", function(data) {
		$('#content').append(data);
		$('#modal-carousel').carousel({interval:false});
	});

	$('.gallery .thumbnail .img-frame a').click(function(e){
		e.preventDefault();

		var clickedA = this;

		$('.carousel-inner').empty();
		$('.gallery .thumbnail .img-frame a').each(function() {
			var currentA = this;
			var itemDiv = createCarouselItem(currentA);

			if(currentA == clickedA){
				itemDiv.addClass('active');
			}
		});

		$('#gallery-modal').modal('show');
		$('#modal-carousel').carousel();
	});
});

function createCarouselItem(a){
	var thumbnailHref = $(a).attr('href');

	var itemDiv = $('<div class="carousel-item"></div>');
	itemDiv.appendTo('.carousel-inner');

	var img = $('<img />');
	img.attr('src', thumbnailHref);
	img.addClass('d-block');
	img.addClass('w-100');
	img.appendTo(itemDiv);

	return itemDiv;
}
