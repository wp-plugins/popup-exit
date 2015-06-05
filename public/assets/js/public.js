jQuery(document).ready(function($) { 
  $(".chch-pfc-close").click(function(e) {
    e.preventDefault();
    chchPopUpID = $(this).attr('data-modalID');
    controlViews = $(this).attr('data-views-control');
    controlExpires = $(this).attr('data-expires-control');
    if (controlViews === 'yes' && controlExpires != 'refresh') {
      if (!Cookies.get('shown_modal_' + chchPopUpID)) {
        switch (controlExpires) {
        case 'session':
          Cookies.set('shown_modal_' + chchPopUpID, 'true', {
            path: '/'
          });
          break;
        }
      }
    }
    $("#modal-" + chchPopUpID).hide("fast");
  });
});