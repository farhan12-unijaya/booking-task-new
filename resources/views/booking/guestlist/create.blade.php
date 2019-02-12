<!-- Modal -->
<div class="modal fade" id="modal-addGeneral" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Tambah <span class="bold">Guestlist</span></h5>
                <small class="text-muted">Sila isi maklumat pada ruangan di bawah.</small>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
		<div class="modal-body m-t-20">
			<form id="form-add-general" role="form" method="post" action="{{ route('guestlist.insert.form') }}">
			{!! csrf_field() !!}

            <div>
            
           
				
            @include('components.input', [
	                    'name' => 'name',
	                    'label' => 'Nama',
	                    'mode' => 'required',
                       
	                ])
            @include('components.input', [
	                    'name' => 'email',
	                    'label' => 'Email',
	                    'mode' => 'required',
                       
	                ])
            
            
			@include('components.input', [
	                    'name' => 'reminder',
	                    'label' => 'Day Reminder',
	                    'mode' => 'required',
						'type' => 'number',
	                ])
               
            @include('components.input', [
	                    'name' => 'rsvp',
	                    'label' => 'RSVP',
	                    'mode' => 'required',
						'type' => 'number',
	                ])
                
	              

			
                <input type="hidden" name="booking_id" value="{{$booking->id}}">	
			


					
                    <button type="button" class="btn btn-info " onclick="submitForm('form-add-general')"><i class="fa fa-check m-r-5"></i> Hantar</button>
	            </div>
				
			</form>
		</div>
			<div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
               
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


