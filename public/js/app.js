/**
 * Created by Ariel on 05/11/2016.
 */
$(document).ready(function()
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });
});