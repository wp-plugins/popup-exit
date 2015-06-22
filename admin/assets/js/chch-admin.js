jQuery(document).ready(function($) {

  var newsletter_adapter = $('#_chch_pop_up_time_newsletter_adapter').find(":selected").val();
  var tabs = '<h2 class="nav-tab-wrapper" id="cc-pu-tabs"><a class="nav-tab nav-tab-active" href="#" title="Templates" data-target="cc-pu-tab-1">Templates</a><a class="nav-tab" href="#" title="Settings" data-target="cc-pu-tab-2">Settings</a>';

  if (newsletter_adapter == 'Database') {
    tabs = tabs + '<a class="nav-tab" href="#" title="Database" data-target="cc-pu-tab-3">WordPress Database</a>';
  }

  tabs = tabs + '</h2>';
  $('#post').prepend(tabs);

  $('#wpbody-content > .wrap').prepend('<a class="button button-secondary right button-hero" style="margin: 25px 0px 0px 2px; padding: 0px 20px; height: 47px;" href="https://shop.chop-chop.org/contact" target="_blank">Contact Support</a>');

  $('#cc-pu-tabs a').on('click', function(e) {
    e.preventDefault();
    var target = $(this).attr('data-target');

    if (!$(this).hasClass('nav-tab-active')) {
      $('.cc-pu-tab').hide();
      $('#cc-pu-tabs a').removeClass('nav-tab-active');
      $(this).addClass('nav-tab-active');

      $('.' + target).show();
    }
  });

  $('.chch-pfc-template-acivate').on('click', function(e) {
    e.preventDefault();
    var template = $(this).attr('data-template');
    var base = $(this).attr('data-base');

    $('#poststuff .theme-browser .theme.active').removeClass('active');
    var theme = $(this).closest('.theme');
    theme.addClass('active');

    $('#_chch_pfc_template').val(template);
    $('#_chch_pfc_template_base').val(base);
    $('#publish').trigger('click');
  });

  $('.cc-pu-customize-close').on('click', function(e) {
    e.preventDefault();
    var template = $(this).attr('data-template');

    $('#cc-pu-customize-form-' + template).hide();
  });

  $('.chch-pfc-template-edit').on('click', function(e) {
    e.preventDefault();
    var thisEl = $(this);
    template = thisEl.attr('data-template');
    base = thisEl.attr('data-base');
    id = thisEl.attr('data-postid');
    nounce = thisEl.attr('data-nounce');

    $.ajax({
      url: chch_pfc_ajax_object.ajaxUrl,
      async: true,
      type: "POST",
      data: {
        action: "chch_pfc_load_preview_module",
        template: template,
        base: base,
        nounce: nounce,
        id: id

      },
      success: function(data) {

        if (!$('#' + template + '-css').length) {
          $('head').append('<link rel="stylesheet" id="' + template + '-css"  href="' + chch_pfc_ajax_object.chch_pop_up_url + 'public/templates/' + base + '/' + template + '/css/style.css" type="text/css" media="all" />');
        }


        if (!$('#chch-pcp-css').length) {
          $('head').append('<link rel="stylesheet" id="chch-pcp-css"  href="' + chch_pfc_ajax_object.chch_pop_up_url + 'public/templates/m-1/css/base.css" type="text/css" media="all" />');
        }

        theme = thisEl.closest('.theme');
        previewWrapper = $('#cc-pu-customize-form-' + template);
        $('#cc-pu-customize-preview-' + template).html(data);

        $('.theme').removeClass('active');
        theme.addClass('active');

        $('#_chch_pfc_template').val(template);
        $('#_chch_pfc_template_base').val(base);
        previewWrapper.find('.select-class-switcher').trigger('change');
        previewWrapper.find('.revealer-wrapper .cc-pu-customize-style').addClass('disable-option');
        previewWrapper.find('.revealer-wrapper.cc-pu-option-active .cc-pu-customize-style').removeClass('disable-option');
        previewWrapper.find('.cc-pu-customize-style').not('.disable-option').trigger('change');

        previewWrapper.show();
      }
    });
  });


  function IsJsonString(str) {
    try {
      JSON.parse(str);
    } catch (e) {
      return false;
    }
    return true;
  }

  /////////////
  //LIVE PREVIEW
  /////////////

  $(".accordion-section-title").on('click', function(e) {
    var el = $(this);
    var target = el.next('.accordion-section-content');
    if (!$(this).hasClass('open')) {
      $(".accordion-section-title").removeClass('open');
      el.addClass('open');
      target.slideDown('fast');
    } else {
      el.removeClass('open');
      target.slideUp('fast');
    }
  });

  $('.cc-pu-colorpicker').wpColorPicker({
    change: _.throttle(function() {
      var el = $(this);
      var template = el.attr('data-template');
      var target = el.attr('data-customize-target');
      var styleAttr = el.attr('data-attr');
      var elValue = el.val();
      $('#cc-pu-customize-preview-' + template + ' ' + target).css(styleAttr, elValue);
    })
  });

  $('.cc-pu-customize-style').on('change', function(e) {
    var el = $(this);

    var elId = el.attr('id');
    var elType = el.attr('type');
    var template = el.attr('data-template');
    var target = el.attr('data-customize-target');
    var styleAttr = el.attr('data-attr');
    var elValue = el.val();
    var elUnit = el.attr('data-unit');

    if (typeof elUnit === "undefined") {
      elUnit = '';
    }

    if (styleAttr == 'background-image') {
      $('#cc-pu-customize-preview-' + template + ' ' + target).css('background-image', 'url(' + elValue + ')');

      var n = elId.search("_image");
      if (n > 0) {
        $('#cc-pu-customize-preview-' + template + ' ' + target).css('background-size', 'cover');
      }
    } else {
      $('#cc-pu-customize-preview-' + template + ' ' + target).css(styleAttr, elValue + elUnit);
    }

  });

  $('.cc-pu-customize-content').on('keyup', function(e) {
    var el = $(this);
    var template = el.attr('data-template');
    var target = el.attr('data-customize-target');
    var elAttr = el.attr('data-attr');
    var elValue = el.val();

    if (el.hasClass('remover')) {
      if (elValue == '') {
        $('#cc-pu-customize-preview-' + template + ' ' + target).hide();
      } else {
        $('#cc-pu-customize-preview-' + template + ' ' + target).show();
      }
    } else if (typeof elAttr === "undefined") {
      $('#cc-pu-customize-preview-' + template + ' ' + target).text(elValue);
    } else {
      $('#cc-pu-customize-preview-' + template + ' ' + target).attr(elAttr, elValue);
    }
  });


  $('.revealer').on('change', function() {
    var el = $(this);
    var target = el.attr('data-customize-target');

    if (el.hasClass('active')) {
      $('#' + target).slideUp('fast');
      el.removeClass('active');
    } else {
      $('#' + target).slideDown('fast');
      el.addClass('active');
    }
  });

  $('.revealer-group').on('change', function() {
    var el = $(this);
    var template = el.attr('data-template');
    var eltarget = el.attr('data-customize-target');
    var elAttr = el.attr('data-attr');

    var group = el.attr('data-group');
    var thisOption = el.find(":selected");
    var target = thisOption.val();

    $('#cc-pu-customize-preview-' + template + ' ' + eltarget).css('background-size', 'auto');
    if (target == 'no') {
      $('#cc-pu-customize-preview-' + template + ' ' + eltarget).css(elAttr, 'url()');

    }

    $('#cc-pu-customize-form-' + template + ' .' + group).slideUp();
    $('#cc-pu-customize-form-' + template + ' #' + target).slideDown();
    $('#cc-pu-customize-form-' + template + ' #' + target).find('.cc-pu-customize-style').trigger('change');

  });

  $('.cc-pu-fonts').on('change', function() {
    var el = $(this);
    template = el.attr('data-template');
    target = el.attr('data-customize-target');
    styleAttr = el.attr('data-attr');

    thisOption = el.find(":selected");

    elValue = thisOption.val();
    linkID = elValue.replace(/\+/g, '-');

    if (!$('#' + linkID).length) {
      $('head').append('<link rel="stylesheet" id="' + linkID + '"  href="//fonts.googleapis.com/css?family=' + elValue + '" type="text/css" media="all" />');
    }

    elValue = elValue.replace(/\+/g, ' ');

    setTimeout(

    function(elValue, template, target) {
      return function() {
        $('#cc-pu-customize-preview-' + template + ' ' + target).css(styleAttr, "\"" + elValue + "\"");
      };
    }(elValue, template, target), 2000);
  });

  $(".select-class-switcher").on('change', function() {
    el = $(this);
    template = el.attr('data-template');
    eltarget = el.attr('data-customize-target');

    elOldVal = el.attr('data-old');
    elval = el.find(":selected").val();

    $('#cc-pu-customize-preview-' + template + ' ' + eltarget).removeClass(elOldVal);
    $('#cc-pu-customize-preview-' + template + ' ' + eltarget).addClass(elval);

    el.attr('data-old', elval);
  });

  /////////////
  //WP UPLOADER
  /////////////
  var custom_uploader;


  $('.cc-pu-image-upload').click(function(e) {

    e.preventDefault();
    target = $(this).attr('data-target');
    //If the uploader object has already been created, reopen the dialog
    if (custom_uploader) {
      custom_uploader.open();
      return;
    }

    //Extend the wp.media object
    custom_uploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
        text: 'Choose Image'
      },
      multiple: false
    });

    //When a file is selected, grab the URL and set it as the text field's value
    custom_uploader.on('select', function() {
      attachment = custom_uploader.state().get('selection').first().toJSON();

      $('#' + target).val(attachment.url);
      $('#' + target).trigger('change');
    });

    //Open the uploader dialog
    custom_uploader.open();

  });

});