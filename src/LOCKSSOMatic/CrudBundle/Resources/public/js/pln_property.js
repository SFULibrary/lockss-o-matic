
/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
