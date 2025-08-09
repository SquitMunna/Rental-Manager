(function($){
  // Booking submit
  $(document).on('submit', '.rntmgr-booking-form', function(e){
    e.preventDefault();
    const $form = $(this);
    const data = $form.serialize();
    const $msg = $form.find('.rntmgr-msg').text('Processing...');

    $.post(rntmgr.ajaxurl, data, function(resp){
      if (resp.success) {
        $msg.text(resp.data.message);
      } else {
        $msg.text(resp.data && resp.data.message ? resp.data.message : 'Error');
      }
    });
  });

  // Availability calendar (simple: mark booked ranges as text list or minimal UI)
  function renderCalendar($wrap, ranges) {
    // Minimal: just list booked ranges
    const $cal = $wrap.find('.rntmgr-calendar').empty();
    if (!ranges.length) { $cal.text('No bookings. All dates available.'); return; }
    $cal.append('<ul class="rntmgr-booked-list"></ul>');
    ranges.forEach(r => {
      $cal.find('ul').append('<li>Booked: ' + r.checkin + ' → ' + r.checkout + '</li>');
    });
  }

  $(function(){
    $('.rntmgr-availability').each(function(){
      const $wrap = $(this);
      const roomId = $wrap.data('room');
      $.get(rntmgr.ajaxurl, { action: 'rntmgr_get_availability', room_id: roomId }, function(resp){
        if (resp.success) renderCalendar($wrap, resp.data.ranges || []);
      });
    });
  });

  // Admin media for gallery buttons
  $(document).on('click', '#room_gallery_btn, #building_gallery_btn', function(e){
    e.preventDefault();
    const isRoom = this.id === 'room_gallery_btn';
    const fieldId = isRoom ? '#room_gallery_ids' : '#building_gallery_ids';
    const preview = isRoom ? '#room_gallery_preview' : '#building_gallery_preview';
    let frame = wp.media({ title: 'Select Images', multiple: true, library: { type: 'image' } });
    frame.on('select', function(){
      const selection = frame.state().get('selection').toArray();
      const ids = selection.map(a => a.id);
      $(fieldId).val(ids.join(','));
      const $prev = $(preview).empty();
      selection.forEach(a => {
        $prev.append('<img src="'+ a.attributes.sizes.thumbnail.url +'" style="margin:4px;" />');
      });
    });
    frame.open();
  });

})(jQuery);
