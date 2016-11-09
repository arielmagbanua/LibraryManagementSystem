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
            form.find('.modal-cancel-button').removeAttr('disabled');

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
            var successModal = $('#success_modal');
            var successModalMessage = $('#success_modal_message');

            switch(data.process)
            {
                case 'update_user':

                    //update the row in the datatable
                    var updatedItem = data.data;
                    var id = data.id;

                    //row base ID
                    var rowBaseID = '#member-'+id+'-';

                    //get all the data from row
                    $(rowBaseID+'first_name').html(updatedItem.first_name);
                    $(rowBaseID+'middle_name').html(updatedItem.middle_name);
                    $(rowBaseID+'last_name').html(updatedItem.last_name);
                    $(rowBaseID+'address').html(updatedItem.address);
                    $(rowBaseID+'email').html(updatedItem.email);
                    $(rowBaseID+'birth_date').html(updatedItem.birth_date);

                    form.find('.modal-form-submit-button').removeAttr('disabled');
                    form.find('.modal-cancel-button').removeAttr('disabled');
                    saveLabel.removeClass('fa fa-spin fa-spinner');
                    saveLabel.html('Save Changes');

                    //close the modal and open the success modal
                    $(form.data('parentmodal')).modal('hide');

                    console.log(form.data('success'));
                    successModalMessage.html(form.data('success'));
                    successModal.modal('show');

                    break;

                case 'update_book':

                    //update the row in the datatable
                    var updatedItem = data.data;
                    var id = data.id;

                    //row base ID
                    var rowBaseID = '#book-'+id+'-';

                    //get all the data from row
                    $(rowBaseID+'title').html(updatedItem.title);

                    //get the display value if the author_id
                    var author =  $("#author_id option:selected").text();
                    var authorTD = $(rowBaseID+'author');
                    authorTD.html(author);
                    authorTD.data('author',data.author_id);

                    $(rowBaseID+'isbn').html(updatedItem.isbn);
                    $(rowBaseID+'quantity').html(updatedItem.quantity);
                    $(rowBaseID+'overdue_fine').html(updatedItem.overdue_fine);
                    $(rowBaseID+'shelf_location').html(updatedItem.shelf_location);

                    form.find('.modal-form-submit-button').removeAttr('disabled');
                    form.find('.modal-cancel-button').removeAttr('disabled');
                    saveLabel.removeClass('fa fa-spin fa-spinner');
                    saveLabel.html('Save Changes');

                    //close the modal and open the success modal
                    $(form.data('parentmodal')).modal('hide');

                    console.log(form.data('success'));
                    successModalMessage.html(form.data('success'));
                    successModal.modal('show');

                    break;

                case 'update_author':

                    //update the row in the datatable
                    var updatedItem = data.data;
                    var id = data.id;

                    //row base ID
                    var rowBaseID = '#author-'+id+'-';

                    //get all the data from row
                    $(rowBaseID+'first_name').html(updatedItem.first_name);
                    $(rowBaseID+'middle_name').html(updatedItem.middle_name);
                    $(rowBaseID+'last_name').html(updatedItem.last_name);
                    $(rowBaseID+'description').html(updatedItem.description);
                    $(rowBaseID+'birth_date').html(updatedItem.birth_date);

                    form.find('.modal-form-submit-button').removeAttr('disabled');
                    form.find('.modal-cancel-button').removeAttr('disabled');
                    saveLabel.removeClass('fa fa-spin fa-spinner');
                    saveLabel.html('Save Changes');

                    //close the modal and open the success modal
                    $(form.data('parentmodal')).modal('hide');

                    console.log(form.data('success'));
                    successModalMessage.html(form.data('success'));
                    successModal.modal('show');

                    break;

                default :
                    table.ajax.reload(function(){

                        form.find('.modal-form-submit-button').removeAttr('disabled');
                        form.find('.modal-cancel-button').removeAttr('disabled');
                        saveLabel.removeClass('fa fa-spin fa-spinner');
                        saveLabel.html('Save');

                        //close the modal and open the success modal
                        $(form.data('parentmodal')).modal('hide');

                        console.log(form.data('success'));
                        successModalMessage.html(form.data('success'));
                        successModal.modal('show');

                    },false);
            }
        }
    });

});
