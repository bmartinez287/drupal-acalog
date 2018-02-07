/**
 * @file
 * Contains js for the Acalog widget.
 */

(function ($) {

  'use strict';

  $(function () {
    $('.acalog').acalogWidgetize({
      gateway: 'http://catalog.volstate.edu'
    });
  });
})(jQuery);
