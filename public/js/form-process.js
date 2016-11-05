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

    $('.modal_error_wrapper').hide();
});

$(document).on('submit','.modal_form',function(e)
{
    e.preventDefault();

    var form = $(this);
    var formData = form.serialize();
    var url = form.attr('action');
    var method = form.attr('method');

    var saveLabel = form.find('span.save-label');
    saveLabel.html('');
    saveLabel.addClass('fa fa-spin fa-spinner');
    form.find('.modal-form-submit-button').attr('disabled','true');
    form.find('.modal-cancel-button').attr('disabled','true');

    $.ajax({

        type: method,
        data: formData,
        url: url,

        error: function(data)
        {

            var errors = data.responseJSON;
            var process = form.data('process');

            console.log(errors);
            form.find('.this_errors').html('');
            form.find('.modal_error_wrapper').show();
            form.find('.this_errors').show();

            var errorsHtml = '<ul>';
            $.each( errors, function( key, value )
            {
                if(value!='')
                {
                    errorsHtml += '<li> <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> ' + value[0] + '</li>'; //showing only the first error.
                }

                $('label[for="'+key+'"]').addClass('error_label error');
                $('#'+key).addClass('error_field error');
            });

            errorsHtml += '</ul>';

            console.log(errorsHtml);

            form.find('.this_errors').append(errorsHtml);
            form.find('.modal-form-submit-button').removeAttr('disabled');

            saveLabel.html('Save');
            saveLabel.removeClass('fa fa-spin fa-spinner');

        },
        success: function(data)
        {
            //form.find('input[type="submit"]').prop('disabled',true);
            form.find('.error').each(function(){
                $(this).removeClass('error');
                $(this).removeClass('error_field');
                $(this).removeClass('error_label');
            });

            $('.modal_error_wrapper').hide();

            //reload the datatable
            var table = $(form.data('datatable')).DataTable();
            table.ajax.reload(function(){

                form.find('.modal-form-submit-button').removeAttr('disabled');
                form.find('.modal-cancel-button').removeAttr('disabled');
                saveLabel.removeClass('fa fa-spin fa-spinner');
                saveLabel.html('Save');

                //close the modal and open the success modal
                $(form.data('parentmodal')).modal('hide');

                console.log(form.data('success'));

                var successModal = $('#success_modal');
                $('#success_modal_message').html(form.data('success'));
                successModal.modal('show');

            },false);
        }
    });

});
