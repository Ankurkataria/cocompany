<!-- delete Modal -->
<div id="order-return-model" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Order Return Confirmation')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{translate('Are you sure to return this?')}}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a href="" id="order-return-link" class="btn btn-primary mt-2">{{translate('Return order')}}</a>
            </div>
        </div>
    </div>
</div><!-- /.modal -->