<!-- Modal -->
<div class="modal fade" id="modal-addGeneral" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle"><span class="bold">Upload Guestlist</span></h5>
                <small class="text-muted">Sila muat naik XLS pada ruangan di bawah.</small>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-t-20">
                <form enctype="multipart/form-data" class="attachment dropzone no-margin">
                    <div class="fallback">
                        <input name="file" type="file" multiple/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-info" onclick="submit()"><i class="fa fa-check m-r-5"></i> Hantar</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

$('#modal-addGeneral').modal('show');
$(".modal form").validate();



$("#form-add-general").submit(function(e) {

// if(!e.isDefaultPrevented()){
// 	console.log('tess');
// }
e.preventDefault();
var form = $(this);


if(!form.valid())
   return;




$.ajax({
    url: form.attr('action'),
    type: 'POST',
    method: form.attr('method'),
    data: new FormData(form[0]),
    dataType: 'json',
    async: true,
    contentType: false,
    processData: false,
    success: function(data) {
        
        swal(data.title, data.message, data.status);
        $("#modal-addGeneral").modal("hide");
        table.api().ajax.reload(null, false);
    },
    error: function(json){
        alert('ada error', json.status);
    }
});
});


$(".datepicker").datepicker({
        language: 'ms',
        format: 'dd/mm/yyyy',
        autoclose: true,
        onClose: function() {
            $(this).valid();
        },
    }).on('changeDate', function(){
        $(this).trigger('change');
    });


</script>


