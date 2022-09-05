$(document).ready(function(){
  if($("#back-top-left").length) {
    $("#back-top-left").hide();
    $(function () {
      $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
          $('#back-top-left').fadeIn();
        } else {
          $('#back-top-left').fadeOut();
        }
      });
      $('#back-top-left a').click(function () {
        $('body,html').animate({
          scrollTop: 0
        }, 800);
        return false;
      });
    });
  }

  if($("#back-top-right").length) {
    $("#back-top-right").hide();
    $(function () {
      $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
          $('#back-top-right').fadeIn();
        } else {
          $('#back-top-right').fadeOut();
        }
      });
      $('#back-top-right a').click(function () {
        $('body,html').animate({
          scrollTop: 0
        }, 800);
        return false;
      });
    });
  }
});