$( document ).ready(function(){
    $('.fadein .review-slide').hide();
	
	var reviews = $('.review-slide');
	var i = 0;
	reviews.eq(i).fadeIn();	
    setInterval( function(){
     				reviews.eq(i).fadeOut();
	  				i++;
	  				if(i == reviews.length){
		 				i = 0; 
	  				}
					setTimeout ( function () {reviews.eq(i).fadeIn();},500);
				}, 9000);
});