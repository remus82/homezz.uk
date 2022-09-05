// WE MUST COUNT NEW ITEMS
var newItemCounter = 2;


$(document).ready(function(){

  // ADD DATE PICKER FUNCTIONALITY
  $('#dt_date, #dt_due_date, #start_date, #end_date').datepicker({
    dateFormat: "d. M yy"
  });


  // UPDATE PRICES BASED ON CHANGE
  $('body').on('change keyup', '.mb-inside.mb-invoice input, .mb-inside.mb-invoice select, .mb-inside.mb-invoice textarea', function(){
    invUpdatePrices();
  });


  // DISABLE GENERATE & SEND MAIL BUTTON AFTER MODIFICATION
  $('body').on('change', '.mb-inside.mb-invoice input, .mb-inside.mb-invoice select, .mb-inside.mb-invoice textarea', function(){
    $('.mb-button-email, .mb-button-pdf').addClass('mb-disabled').attr('disabled', true).attr('title', invTitleBlock).attr('onclick', 'return false;');

    Tipped.create('.mb-button-email, .mb-button-pdf', { maxWidth: 200, radius: false, size: 'medium', behavior: 'hide' });
  });


  // ADD NEW ITEM LINE
  $('body').on('click', 'a.mb-add-item', function(e){
    e.preventDefault();

    $('.mb-items .mb-r:last').after($('.mb-ph-row').html());

    $('.mb-items .mb-r:last input, .mb-items .mb-r:last textarea').each(function(){
      $(this).attr('name', $(this).attr('name') + '-' + newItemCounter);
      $(this).prop('required', true);
    });

    newItemCounter++;
  });


  // SHOW LINE FUNCTIONALITY
  $('body').on('click', '.mb-activation a', function(e){
    e.preventDefault();

    var cls = 'mb-' + $(this).attr('data-class');

    $('.mb-row.' + cls).show(0);
    $(this).hide(0);
  });


  // HIDE LINE
  $('body').on('click', '.mb-line-remove a', function(e){
    e.preventDefault();

    var cls = 'a.mb-show-' + $(this).closest('.mb-row').attr('data-class');

    $(this).closest('.mb-row').find('input').val(0.0).change();
    $(this).closest('.mb-row').hide(0);
    $(cls).show(0);
  });


  // REMOVE ITEM
  $('body').on('click', '.mb-item-remove a', function(e){
    e.preventDefault();

    $(this).closest('.mb-r').remove();
    $('input[name="f_amount"]').change();
  });


  // MAIL SEND, GET THE EMAIL
  $('body').on('click', '.mb-button-quatro:not(.mb-disabled)', function(e){
    e.preventDefault();
    var url = this.href + '&email_enter=' + $('input[name="s_email"]').val();

    window.location.replace(url);
  });




  // USER LOOKUP WALLET
  var name = $('.mb-user-lookup input[name="s_user_name"]');

  if(name.length) {
    name.prop('autocomplete', 'off');
    
    name.autocomplete({
      source: user_lookup_base,
      minLength: 0,
      select: function (event, ui) {
        if (ui.item.id == '') {
          return false;
        } else {
          $.getJSON(
            user_lookup_url + ui.item.id,
            {'fk_i_user_id': name.val()},
            function(data){
              if(data.user.id != 0) {
                $('#fk_i_user_id').val(data.user.id);
                $('#s_user_name').val(data.user.name);
                $('#s_email').val(data.user.email);

                if($('#s_to').val() == '' && data.location != '') {
                  $('#s_to').val(data.location);
                }
              } else {
                $('.mb-error-block').val(user_lookup_error);
              }
            }
          );
        }

        $('#id').val(ui.item.id);
      },
      search: function () {
        $('#id').val('');
      }
    });

    $('.ui-autocomplete').css('zIndex', 10000);
  }


 
  // CATEGORY MULTI SELECT
  $('body').on('change', '.mb-row-select-multiple select', function(e){
    $(this).closest('.mb-row-select-multiple').find('input[type="hidden"]').val($(this).val());
  });



  // ON LOCALE CHANGE RELOAD PAGE
  $('body').on('change', 'select.mb-select-locale', function(e){
    window.location.replace($(this).attr('rel') + "&invLocale=" + $(this).val());
  });


  // HELP TOPICS
  $('#mb-help > .mb-inside > .mb-row.mb-help > div').each(function(){
    var cl = $(this).attr('class');
    $('label.' + cl + ' span').addClass('mb-has-tooltip').prop('title', $(this).text());
  });

  $('.mb-row label').click(function() {
    var cl = $(this).attr('class');
    var pos = $('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).offset().top - $('.navbar').outerHeight() - 12;;
    $('html, body').animate({
      scrollTop: pos
    }, 1400, function(){
      $('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).addClass('mb-help-highlight');
    });

    return false;
  });


  // ON-CLICK ANY ELEMENT REMOVE HIGHLIGHT
  $('body, body *').click(function(){
    $('.mb-help-highlight').removeClass('mb-help-highlight');
  });


  // GENERATE TOOLTIPS
  Tipped.create('.mb-has-tooltip', { maxWidth: 200, radius: false, behavior: 'hide' });
  Tipped.create('.mb-has-tooltip-user', { maxWidth: 350, radius: false, size: 'medium', behavior: 'hide' });
  Tipped.create('.mb-has-tooltip-light', { maxWidth: 200, radius: false, size: 'medium', behavior: 'hide' });


  // CHECKBOX & RADIO SWITCH
  $.fn.bootstrapSwitch.defaults.size = 'small';
  $.fn.bootstrapSwitch.defaults.labelWidth = '0px';
  $.fn.bootstrapSwitch.defaults.handleWidth = '50px';

  $(".element-slide").bootstrapSwitch();



  // MARK ALL
  $('input.mb_mark_all').click(function(){
    if ($(this).is(':checked')) {
      $('input[name^="' + $(this).val() + '"]').prop( "checked", true );
    } else {
      $('input[name^="' + $(this).val() + '"]').prop( "checked", false );
    }
  });


});


var timeoutHandle;

function inv_message($html, $type = '') {
  window.clearTimeout(timeoutHandle);

  $('.mb-message-js').fadeOut(0);
  $('.mb-message-js').attr('class', '').addClass('mb-message-js').addClass($type);
  $('.mb-message-js').fadeIn(200).html('<div>' + $html + '</div>');

  var timeoutHandle = setTimeout(function(){
    $('.mb-message-js > div').fadeOut(300, function() {
      $('.mb-message-js > div').remove();
    });
  }, 10000);
}



// UPDATE PRICES ON CHANGE
function invUpdatePrices() {
  var amount = 0;
  var balance = 0;
  var subtotal = 0;
  var total = 0;
  var denominator = parseInt("1" + "0".repeat(invDecimals));

  if(denominator <= 0) {
    denominator = 1;
  }

  var discount = parseFloat($('input[name="f_discount"]').val());
  var tax = parseFloat($('input[name="f_tax"]').val());
  var fee = parseFloat($('input[name="f_fee"]').val());
  var shipping = parseFloat($('input[name="f_shipping"]').val());
  var paid = parseFloat($('input[name="f_paid"]').val());

  // Calculate subtotal
  if($('.mb-r').length) {
    $('.mb-r').each(function() {
      var item_rate = parseFloat($(this).find('.invitem_rate').val());
      var item_quantity = parseFloat($(this).find('.invitem_quantity').val());

      if(isNaN(item_rate)) { item_rate = 0; }
      if(isNaN(item_quantity)) { item_quantity = 0; }

      var item_amount = item_rate * item_quantity;

      subtotal = subtotal + item_amount;

      item_amount = Math.round(item_amount*denominator)/denominator;

      $(this).find('.mb-price-box span').text(item_amount.toFixed(invDecimals));
    });
  }

  subtotal = Math.round(subtotal*denominator)/denominator;

  $('span.inv-subtotal').text(subtotal.toFixed(invDecimals));


  if(isNaN(discount)) { discount = 0; }
  if(isNaN(tax)) { tax = 0; }
  if(isNaN(fee)) { fee = 0; }
  if(isNaN(shipping)) { shipping = 0; }
  if(isNaN(paid)) { paid = 0; }

  
  amount = amount + subtotal;
  amount = amount * (100 - discount)/100;
  //amount = amount + (amount - amount/((100 + tax)/100));
  amount = amount * ((100 + tax)/100);
  amount = amount + fee;
  amount = amount + shipping;

  balance = amount - paid;

  balance = Math.round(balance*denominator)/denominator;
  amount = Math.round(amount*denominator)/denominator;

  $('input[name="f_balance"]').val(balance.toFixed(invDecimals));
  $('input[name="f_amount"]').val(amount.toFixed(invDecimals));
}



