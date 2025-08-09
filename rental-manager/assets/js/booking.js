jQuery(function ($) {
  const $form = $('#rntmgr-booking-form');
  const $resp = $('#rntmgr-response');

  if (!$form.length) return;

  $form.on('submit', function (e) {
    e.preventDefault();

    const data = $form.serializeArray().reduce((obj, item) => {
      obj[item.name] = item.value;
      return obj;
    }, {});

    // Add required AJAX fields
    data.action = 'rntmgr_submit_booking';
    data.nonce = rntmgr_ajax.nonce;

    $resp.html('Submitting...');

    $.post(rntmgr_ajax.ajax_url, data)
      .done(function (response) {
        if (response && response.success) {
          $resp.html('<p>' + response.data.message + '</p>');
          $form[0].reset();
        } else {
          const msg = response && response.data ? response.data.message : 'Booking failed.';
          $resp.html('<p>' + msg + '</p>');
        }
      })
      .fail(function () {
        $resp.html('<p>Network error. Please try again.</p>');
      });
  });
});
