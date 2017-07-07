/**
 * PLN Properties are multi-valued. This bit of javascript adds buttons to
 * add and remove values.
 *
 * @param jQuery $
 * @returns null
 */
(function ($) {
    var $addLink = $("<a href='#' class='addItem btn btn-primary'><span class='glyphicon glyphicon-plus'></span> Add</span>");

    function addValueForm($container, count) {
        var prototype = $container.data('prototype');
        var index = $container.data('count');
        var $form = $(prototype.replace(/__name__/g, index).replace(/label__/g, ''));
        $form.find('label').append(" <a href='#' class='delItem'>Remove</a>");
        $container.append($form);
        $('.delItem').click(function (e) {
            e.preventDefault();
            $(this).closest('div').remove();
        });
        $container.data('count', index + 1);
    }

    $(document).ready(function () {
        var $container = $('div[data-prototype]');
        $container.after($addLink);
        $container.data('count', $container.find('div.form-group').length);
        $addLink.click(function (e) {
            e.preventDefault();
            addValueForm($container);
        });
        $container.find('label').each(function () {
            var $this = $(this);
            $this.append(" <a href='#' class='delItem'>Remove</a>");
        });
        $('.delItem').click(function (e) {
            e.preventDefault();
            $(this).closest('div').remove();
        });
    });
})(jQuery);
