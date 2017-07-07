/**
 * Toggle the details display for AU statuses.
 *
 * @param jQuery $
 * @returns null
 */
(function ($) {
    $(".details-toggle").click(function () {
        var id = $(this).data('autoggle');
        $("#" + id).toggle();
    });
})(jQuery);