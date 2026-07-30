$( document ).ready(function(){
  $('.theme-image').css({ height: $(window).innerHeight() });
  $(window).resize(function(){
    $('.theme-image').css({ height: $(window).innerHeight() });
  });
});