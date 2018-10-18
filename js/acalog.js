/**
 * @file
 * Contains js for the Acalog widget.
 */

(function ($) {

  'use strict';

  $(function () {
    $('.acalog').acalogWidgetize({
      gateway: 'https://catalog.volstate.edu'
    });
  });
})(jQuery);
