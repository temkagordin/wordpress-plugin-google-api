jQuery(document).ready(function () {
    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    jQuery('.sheetid').click(function () {
        var id = jQuery(this).attr('id');

        jQuery.ajax({
            url: 'admin.php?page=work_sheet',
            type: 'POST',
            dataType: 'html',
            data: {
                'id': id
            },
            success: function (data) {
                jQuery('#grid').empty();
                jQuery('#grid').html(data);
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    jQuery(document).on('click','.gridid',function(){
        var id = jQuery(this).attr('id');
        var name = jQuery(this).attr('name');
        jQuery.ajax({
            url: 'admin.php?page=work_sheet',
            type: 'POST',
            dataType: 'html',
            data: {
                'gridid': id,
                'name': name
            },
            success: function (data) {
                jQuery('#result').empty();
                jQuery('#result').html(data);
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
});

