<!-- Modal -->
<div class="modal fade" id="modal-addGeneral" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Booking <span class="bold">Ruangan</span></h5>
                <small class="text-muted">Sila isi maklumat pada ruangan di bawah.</small>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
		<div class="modal-body m-t-20">
			<form id="form-add-general" role="form" method="post" action="{{ route('booking.insert.form') }}">
			{!! csrf_field() !!}

            <div>
                                
            <div class="form-group col-md-12 text-center">              
                <img src="{{asset('storage/room_images/thumbnail/'.$url)}}">            
            </div>
            <div class="form-group col-md-12 text-center"> 
              {{$room->name}} 
            </div>
            
            <input type="hidden" name="room_id" value="{{$room->id}}">
            
				
            @include('components.date', [
	                    'name' => 'tanggal',
	                    'label' => 'Tanggal',
	                    'mode' => 'required',
                        'value' => ''
	                ])

                

            <div class="form-group-attached m-b-10">
              <div class="row">
                <div class="col-md-6">

					@include('components.input', [
	                    'name' => 'timefrom',
	                    'label' => 'Dari Pukul',
	                    'mode' => 'required',
						'type' => 'time',
	                ])
                </div>
                <div class="col-md-6">
					@include('components.input', [
	                    'name' => 'timeto',
	                    'label' => 'Sampai Pukul',
	                    'mode' => 'required',
						'type' => 'time',
	                ])
                </div>
                </div>
                </div>
	              

				 <div class="col-sm-12 p-3"/>

                <div class="container">
                <div class=" form-group col-md-12 text-center">
					<button type="button" class="btn btn-info" onclick="check('form-add-general')"><i class="fa fa-check m-r-5"></i> Check Availability and Price</button>
                </div>
                <div class="form-group col-md-12 text-center">
                <button type="button" class="btn btn-info" onclick="submitForm('form-add-general')"><i class="fa fa-check m-r-5"></i> Hantar</button>
                </div>
                </div>
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