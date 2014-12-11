jQuery(document).ready(function($)
{
  var $verify_wrapper = $('#verification-wrapper'),
  $settings_wrapper = $('#settings-wrapper'),
  $select_post = $('select[name=barc_post_id]'),
  $button_settings = $('.button-barc-settings'),
  $verified_username = $('.verified-username'),
  $content_position = $('input[name=content_position]');

  var showMessage = function($message)
  {
    $message.show();
    window.setTimeout(function()
    {
      $message.fadeOut(400);
    }, 4000);
  }

  var setVerified = function(verified)
  {
    if (verified)
    {
      $verify_wrapper.addClass('verified');
      $settings_wrapper.addClass('verified');
      $select_post.attr('disabled', false);
      $button_settings.attr('disabled', false);
      $content_position.attr('disabled', false);
    }
    else
    {
      $verify_wrapper.removeClass('verified');
      $settings_wrapper.removeClass('verified');
      $select_post.attr('disabled', true);
      $button_settings.attr('disabled', true);
      $content_position.attr('disabled', true);
    }
  }

  // activation
  $('button[name=verify_button]').bind('click', function()
  {
    var $message_failed = $('#barc_submit_message_failed');
    var $loader = $('#verify_loader');
    var $t = $(this),
    code = $('input[name=barc_code]').val();

    $t.attr('disabled', true);
    $loader.show();

    $.post(Barc.action_url, { a: 'save_code', code: code }, function(r)
    {
      $t.attr('disabled', false);
      $loader.hide();

      if (r.hasOwnProperty('status') && r.status == 1)
      {
        $verified_username.text(code.toLowerCase());
        setVerified(true);
      }
      else
        showMessage($message_failed);

    }).error(function()
    {
      $t.attr('disabled', false);
      $loader.hide();
      alert(Barc.text.ajax_error);
    });
  });

  // unverify this site
  $('#unverify-link').bind('click', function()
  {
    setVerified(false);
  });


  // change page
  $select_post.bind('change', function()
  {
    var $loader = $('#select_page_loader').show();

    $.post(Barc.action_url, {
      a: 'save_page',
      post_id: $(this).val()
    }, function(r)
    {
      $loader.hide();

    }).error(function()
    {
      $loader.hide();
      alert(Barc.text.ajax_error);
    });
  });

  $content_position.bind('change', function()
  {
    var $loader = $('#select_page_loader').show(),
    v = $content_position.filter(':checked').val();

    $.post(Barc.action_url, {
      a: 'save_position',
      position: v
    }, function(r)
    {
      $loader.hide();

    }).error(function()
    {
      $loader.hide();
      alert(Barc.text.ajax_error);
    });
  });

  var _barcLoaded = false;
  var _barcSource = null;
  var _barcOrigin = null;
  var _barcPlan = "";
  $(".unlock-button").bind("click", function(e){
    if(_barcLoaded == false || $(e.target).closest(".subscription-plan").hasClass("disabled") || !$verify_wrapper.hasClass("verified"))
      return;
    $("#barc-iframe-wrapper").show();
    _barcPlan = $(e.target).closest(".subscription-plan").attr("id");
    _barcSource.postMessage("barc:payment:" + _barcPlan + ":" + $(".verified-username").text(),_barcOrigin);
  });

  window.addEventListener("message", receiveMessage, false);

  function receiveMessage(event)
  {
    if (event.origin.indexOf("barc.com") > -1)
    {
      if (event.data.indexOf("barc:payment") > -1)
      {
        if (event.data.indexOf("prepaid") > -1)
        {
          _barcPlan = event.data.substring(21);
        }
        
        if (event.data.indexOf("paid") > -1)
        {
          $("#barc-iframe-wrapper").hide();
          $.post(Barc.action_url, {
            a: 'save_payment',
            plan: _barcPlan
          }, function(r)
          {
            if(_barcPlan == "monthly")
            {
              $("#monthly").addClass("unlocked");
              $("#yearly").addClass("disabled");
            }
            else if (_barcPlan == "yearly")
            {
              $("#yearly").addClass("unlocked");
              $("#monthly").addClass("disabled");
            }
          }).error(function()
          {
            alert(Barc.text.ajax_error);
          });
        }
        else if (event.data.indexOf("loaded") > -1)
        {
          _barcLoaded = true;
          _barcSource = event.source;
          _barcOrigin = event.origin;
        }
      }
    }
  }

});