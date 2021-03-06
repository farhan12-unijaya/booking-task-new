<!-- Modal -->
<div class="modal fade" id="modal-status" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Kemaskini <span class="bold">Status Permohonan</span></h5>
                <small class="text-muted">Sila isi maklumat pada ruangan di bawah.</small>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-t-20">
            	<form id='form-status' role="form" method="post" action="{{ $route }}">
                    @include('components.date', [
                        'name' => 'status_date',
                        'label' => 'Tarikh Kemaskini Status',
                        'mode' => 'required',
                        'value' => date('d/m/Y'),
                    ])
                	@include('components.textarea', [
                        'name' => 'status_data',
                        'label' => 'Status Permohonan',
                        'mode' => 'required',
                    ])
            	</form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-info" onclick="submitForm('form-status')"><i class="fa fa-check m-r-5"></i> Hantar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#modal-view").modal('hide');
    $('#modal-status').modal('show');
    $("#form-status").validate();

    $(".datepicker").datepicker({
        language: 'ms',
        format : "dd/mm/yyyy",
        autoclose: true,
        onClose: function() {
            $(this).valid();
        },
    }).on('changeDate', function(){
        $(this).trigger('change');
    });

    $("#form-status").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        if(!form.valid())
           return;

        swal({
            title: "Teruskan?",
            text: "Adakah anda pasti untuk kemaskini status permohonan tersebut?",
            icon: "warning",
            buttons: {
                cancel: "Batal",
                confirm: {
                    text: "Teruskan",
                    value: "confirm",
                    closeModal: false,
                    className: "btn-info",
                },
            },
            dangerMode: true,
        })
        .then((confirm) => {
            if (confirm) {
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: new FormData(form[0]),
                    dataType: 'json',
                    async: true,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        swal(data.title, data.message, data.status);
                        $("#modal-status").modal("hide");
                        table.api().ajax.reload(null, false);
                    }
                });
            }
        });

        
    });
</script>