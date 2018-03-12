/**
 * Toggle the box status display page.
 *
 * @param jQuery $
 * @returns void
 */
(function ($) {
    $(".details-toggle").click(function () {
        var id = $(this).data('autoggle');
        $("#" + id).toggle();
    });
})(jQuery);