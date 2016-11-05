<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete Member</h4>
            </div>

            <div class="modal-body">
                {!! Form::hidden('id_to_delete', '', ['id' => 'id_to_delete', 'class' => 'form-control']) !!}
                <p id="delete_modal_message">Are you sure you want to delete this member?</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="delete_cancel_button" data-dismiss="modal">Cancel</button>
                <button type="button" id="delete_button" class="btn btn-danger"><span class="delete-label">Yes Delete</span></button>
            </div>

        </div>
    </div>
</div>