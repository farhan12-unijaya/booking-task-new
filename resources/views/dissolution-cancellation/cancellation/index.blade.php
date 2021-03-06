@extends('layouts.app')
@include('plugins.dropzone')

@section('content')
@include('components.msg-disconnected')
<!-- START BREADCRUMB -->
<div class="jumbotron" data-pages="parallax">
	<div class=" container-fluid container-fixed-lg sm-p-l-0 sm-p-r-0">
		<div class="inner">
			{{ Breadcrumbs::render('cancellation') }}
			<!-- END BREADCRUMB -->
			<div class="row">
				<div class="col-xl-12 col-lg-12 ">
					<!-- START card -->
					<div class="card card-transparent">
						<div class="card-block p-t-0">
							<h3 class='m-t-0'>Laporan Pembatalan Kesatuan Sekerja</h3>
							<p class="small hint-text m-t-5">
								Sila lengkapkan semua maklumat berikut mengikut turutan dan arahan yang dipaparkan.
							</p>
						</div>
					</div>
					<!-- END card -->
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END BREADCRUMB -->
<!-- END JUMBOTRON -->
@include('components.msg-connecting')
<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
	<!-- START card -->
	<div class="card card-transparent">
		<div class="card-block">
			<div>
				<form id="form-formf" class="form-horizontal" role="form" autocomplete="off" method="post" action="{{ route('cancellation.form', $cancellation->id) }}">
                    <div class="form-group row">
                        <label for="fname" class="col-md-3 control-label">Nama Kesatuan
                            <span style="color:red;">*</span>
                        </label>
                        <div class="col-md-9">
                            <select id="user_id" name="user_id" class="full-width" data-init-plugin="select2" >
                                <option disabled selected hidden>Pilih satu..</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->entity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

					@include('components.bs.date', [
						'name' => 'applied_at',
						'label' => 'Tarikh Laporan',
						'mode' => 'required',
						'value' =>  $cancellation->applied_at ? date('d/m/Y', strtotime($cancellation->applied_at)) : '',
					])

				</form>

				<div class="form-group row">
					<label for="fname" class="col-md-3 control-label">Muat Naik Dokumen (Laporan) <span style="color:red;">*</span></label>
					<div class="col-md-9">
						<form action="{{ route('cancellation.form.attachment', $cancellation->id) }}" enctype="multipart/form-data" class="attachment dropzone no-margin">
							<div class="fallback">
								<input name="file" type="file" multiple/>
							</div>
						</form>
					</div>
				</div>

				<br>

				<div class="form-group">
					<button type="button" class="btn btn-default mr-1" onclick="location.href='{{ route('cancellation.list') }}'"><i class="fa fa-angle-left mr-1"></i> Kembali</button>
					<!-- If mode update form change button label to Kemaskini-->
                    @if($cancellation->created_by_user_id == auth()->id())
					<button type="button" class="btn btn-info pull-right" onclick="submit()" data-dismiss="modal"><i class="fa fa-check mr-1"></i> Hantar</button>
                    @endif
                    <button type="button" class="btn btn-default pull-right mr-1" onclick="save()" data-dismiss="modal"><i class="fa fa-save mr-1"></i> Simpan</button>
				</div>

			</div>
    	</div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">

$("#form-formf").validate();

function submit() {
    swal({
        title: "Teruskan?",
        text: "Adakah anda pasti untuk menghantar permohonan ini?",
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
                url: '{{ route("cancellation.form", $cancellation->id) }}',
                method: 'POST',
                dataType: 'json',
                async: true,
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data.status == 'error') {
                        swal(data.title, data.message, data.status);
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                    else {
                        swal({
                            title: data.title,
                            text: data.message,
                            icon: data.status,
                            button: "OK",
                        })
                        .then((confirm) => {
                            if (confirm) {
                                location.href="{{ route('cancellation.list') }}";
                            }
                        });
                    }
                }
            });
        }
    });
}

function save() {
    swal({
        title: "Berjaya!",
        text: "Data yang telah disimpan.",
        icon: "success",
        button: "OK",
    })
    .then((confirm) => {
        if (confirm) {
            location.href="{{ route('cancellation.list') }}";
        }
    });
}

$(".attachment").dropzone({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: "{{ route('cancellation.form.attachment', $cancellation->id) }}",
    addRemoveLinks : true,
    dictRemoveFile: "Padam Fail",
    init: function () {
    	var myDropzone = this;

    	$.ajax({
            url: '{{ route('cancellation.form.attachment', $cancellation->id) }}/',
            method: 'get',
            dataType: 'json',
            async: true,
            contentType: false,
            processData: false,
            success: function(data) {
            	$.each(data, function(key,value){
	                var mockFile = { name: value.name, size: value.size, id: value.id };
					myDropzone.emit("addedfile", mockFile);
					myDropzone.emit("thumbnail", mockFile);

					$(mockFile.previewElement).prop('id', value.id);
				});
            }
        });

    	myDropzone.on("addedfile", function (file) {
            if(file.id) {
                file._downloadLink = Dropzone.createElement("<a class=\"btn btn-default btn-xs\" id=\"bt-down\" style=\"margin-top:5px;\" href=\"{{ url('general/attachment') }}/"+file.id+"/"+file.name+"\" title=\"Muat Turun\" data-dz-download><i class=\"fa fa-download m-r-5\"></i> Muat Turun</a>");
                file.previewElement.appendChild(file._downloadLink);
            }
        });

        $(".dz-remove").addClass('btn', 'btn-danger', 'btn-xs');

    },
    success: function(file, response) {
        file.previewElement.id = response.id;
        file._downloadLink = Dropzone.createElement("<a class=\"btn btn-default btn-xs\" id=\"bt-down\" style=\"margin-top:5px;\" href=\"{{ url('general/attachment') }}/"+response.id+"/"+file.name+"\" title=\"Muat Turun\" data-dz-download><i class=\"fa fa-download m-r-5\"></i> Muat Turun</a>");
        file.previewElement.appendChild(file._downloadLink);
        return file.previewElement.classList.add("dz-success");
    },
    removedfile: function(file) {
    	swal({
	        title: "Padam Data",
	        text: "Data yang telah dipadam tidak boleh dikembalikan. Teruskan?",
	        icon: "warning",
	        buttons: ["Batal", { text: "Padam", closeModal: false }],
	        dangerMode: true,
	    })
	    .then((confirm) => {
	        if (confirm) {
	            $.ajax({
	                url: '{{ route('cancellation.form.attachment', $cancellation->id) }}/'+file.previewElement.id,
	                method: 'delete',
	                dataType: 'json',
	                async: true,
	                contentType: false,
	                processData: false,
	                success: function(data) {
	                    swal(data.title, data.message, data.status);
	                    if(data.status == "success")
	                    	file.previewElement.remove();
	                }
	            });
	        }
	    });
    },
});

$('#dz-entity').on('click', function() {
	console.log("filename :",$('#dz-filename').value());
});

@if(!request()->id)
    window.history.pushState('cancellation', 'Pembatalan Kesatuan Sekerja', '{{ fullUrl() }}/{{ $cancellation->id }}');
@endif

$(document).ready(function(){
    @if($cancellation->entity)
	$("#user_id").val({{ $cancellation->entity->user->id }}).trigger('change');
    @endif

    var socket = io('{{ env('SOCKET_HOST', '127.0.0.1') }}:{{ env('SOCKET_PORT', 3000) }}');

    socket.on('connect', function() {
        $(".msg-disconnected").slideUp();
        $(".msg-connecting").slideUp();
    });

    socket.on('disconnect', function() {
        $(".msg-disconnected").slideDown();
        $("html, body").animate({ scrollTop: 0 }, 500);
    });

    $('#form-formf input, #form-formf select, #form-formf textarea').on('change', function() {
        socket.emit('formf', {
            id: {{ $cancellation->id }},
            name: $(this).attr('name'),
            value: $(this).attr('type') == 'checkbox' ? ($(this).prop('checked') ? 1 : 0) : $(this).val(),
            user: '{{ Cookie::get('api_token') }}'
        });
        console.log('changed');
    });
});

</script>
@endpush
