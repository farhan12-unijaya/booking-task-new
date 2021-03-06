<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
	<!-- START card -->
	<div class="card card-transparent">
		<div class="card-block">
			<form id="form-formw" class="form-horizontal" role="form" autocomplete="off" method="post" action="#">

				@component('components.bs.label', [
					'name' => 'uname',
					'label' => 'Nama Kesatuan',
				])
				{{ $formw->tenure->entity->name }}
				@endcomponent

				@component('components.bs.label', [
					'name' => 'registration_no',
					'label' => 'No. Sijil Pendaftaran',
				])
				{{ $formw->tenure->entity->registration_no }}
				@endcomponent

				<div class="form-group row">
					<label class="col-md-3 control-label">
						Alamat Ibu Pejabat
					</label>
					<div class="col-md-9">
						{!! $formw->address->address1.
                        ($formw->address->address2 ? ',<br>'.$formw->address->address2 : '').
                        ($formw->address->address3 ? ',<br>'.$formw->address->address3 : '').
                        ',<br>'.
                        $formw->address->postcode.' '.
                        ($formw->address->district ? $formw->address->district->name : '').', '.
                        ($formw->address->state ? $formw->address->state->name : '') !!}
					</div>
				</div>

				@include('components.bs.input', [
					'name' => 'consultant_name',
					'label' => 'Nama Badan Perunding',
					'mode' => 'required',
					'value' => $formw->consultant_name,
				])

				@include('components.bs.textarea', [
					'name' => 'consultant_address',
					'label' => 'Alamat Ibu Pejabat Badan Perunding',
					'mode' => 'required',
					'value' => $formw->consultant_address,
				])

				@include('components.bs.input', [
					'name' => 'consultant_phone',
					'label' => 'No Telefon Badan Perunding',
					'mode' => 'required',
					'value' => $formw->consultant_phone,
				])

				@include('components.bs.input', [
					'name' => 'consultant_fax',
					'label' => 'No Fax Badan Perunding',
					'mode' => 'required',
					'value' => $formw->consultant_fax,
				])

				@include('components.bs.input', [
					'name' => 'consultant_email',
					'label' => 'Emel Badan Perunding',
					'mode' => 'required',
					'type' => 'email',
					'value' => $formw->consultant_email,
				])

				<div class="form-group row">
					<label for="fname" class="col-md-3 control-label">Tujuan Penubuhan Badan Perunding <span style="color:red;">*</span></label>
					<div class="col-md-9">
						<div class="input-group">
	                      	<input type="text" class="form-control" placeholder="Tujuan" name="purpose" id="purpose">
	                      	<span class="input-group-addon primary clickable" onclick="addPurpose()" style="background-color: #6d5eac !important"><i class="fa fa-plus"></i></span>
	                    </div>
						<table>
							<table class="table table-hover" id="table-purpose">
								<thead>
									<tr>
										<th class="fit">Bil.</th>
										<th>Tujuan</th>
										<th style="width:10%"></th>
									</tr>
								</thead>
							</table>
						</table>
					</div>
				</div>

				@include('components.bs.date', [
					'name' => 'resolved_at',
					'label' => 'Tarikh Diputuskan',
					'mode' => 'required',
					'value' =>  $formw->resolved_at ? date('d/m/Y', strtotime($formw->resolved_at)) : ''
				])

				<div class="form-group row">
                    <label for="" class="col-md-3 control-label">
                        Melalui (Jenis Mesyuarat)<span style="color:red;">*</span>
                    </label>
                    <div class="col-md-9">
                        <div class="radio radio-primary">
                            @foreach($meeting_types as $meeting_type)
                                <input name="meeting_type_id" value="{{ $meeting_type->id }}" id="meeting_type_{{ $meeting_type->id }}" type="radio" class="hidden" required>
                                <label for="meeting_type_{{ $meeting_type->id }}">{{ $meeting_type->name }}</label>
                            @endforeach
                        </div>
                    </div>
                </div>

				<div class="form-group row">
					<label for="fname" class="col-md-3 control-label">Nama Pemohon <span style="color:red;">*</span></label>
					<div class="col-md-9">
						<div class="input-group">
							<select id="requester" name="requester" class="full-width autoscroll" data-init-plugin="select2" required>
								<option value="" selected="" disabled="">Pilih..</option>
								@foreach($formw->tenure->officers as $requester)
								<option value="{{ $requester->id }}">
									{{ $requester->name }} ( {{ $requester->designation->name }} )
								</option>
								@endforeach
							</select>
	                      	<span class="input-group-addon primary clickable" onclick="addRequester()" style="background-color: #6d5eac !important"><i class="fa fa-plus"></i></span>
	                    </div>
						<table>
							<table class="table table-hover" id="table-requester">
								<thead>
									<tr>
										<th class="fit">No.</th>
										<th>Nama</th>
										<th class="fit">Jawatan</th>
										<th style="width:10%"></th>
									</tr>
								</thead>
							</table>
						</table>
					</div>
				</div>

				@include('components.bs.date', [
					'name' => 'applied_at',
					'label' => 'Tarikh Permohonan',
					'mode' => 'required',
					'value' => $formw->applied_at ? date('d/m/Y', strtotime($formw->applied_at)) : date('d/m/Y')
				])

				<div class="form-group row">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-9">
						<div class="checkbox check-primary">
							<input value="1" name="verified" id="verified" type="checkbox" class="hidden">
							<label for="verified">Dengan ini saya mengesahkan bahawa maklumat yang diberikan adalah benar.</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<ul class="pager wizard no-style">
							<li class="next">
								<button class="btn btn-success btn-cons btn-animated from-left pull-right fa fa-angle-right" type="button">
									<span>Seterusnya</span>
								</button>
							</li>
							<li>
								<button type="button" class="btn btn-default mr-1" onclick="location.href='{{ route('formw.list') }}'"><i class="fa fa-angle-left mr-1"></i> Kembali</button>
							</li>
						</ul>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- END card -->
</div>
<!-- END CONTAINER FLUID -->

@push('js')
<script type="text/javascript">

$("#form-formw").validate();

// Table for purposes
var table = $('#table-purpose');

var settings = {
	"processing": true,
	"serverSide": true,
	"deferRender": true,
	"ajax": "{{ route('formw.form.purpose', $formw->id) }}",
	"columns": [
		{ data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
			return meta.row + meta.settings._iDisplayStart + 1;
		}},
		{ data: "purpose", name: "purpose"},
		{ data: "action", name: "action", orderable: false, searchable: false},
	],
	"columnDefs": [
		{ className: "nowrap", "targets": [ 2 ] }
	],
	"sDom": "<t><'row'<p i>>",
	"destroy": true,
	"scrollCollapse": true,
	"oLanguage": {
		"sEmptyTable":      "Tiada data",
		"sInfo":            "Paparan dari _START_ hingga _END_ dari _TOTAL_ rekod",
		"sInfoEmpty":       "Paparan 0 hingga 0 dari 0 rekod",
		"sInfoFiltered":    "(Ditapis dari jumlah _MAX_ rekod)",
		"sInfoPostFix":     "",
		"sInfoThousands":   ",",
		"sLengthMenu":      "Papar _MENU_ rekod",
		"sLoadingRecords":  "Diproses...",
		"sProcessing":      "Sedang diproses...",
		"sSearch":          "Carian:",
	   "sZeroRecords":      "Tiada padanan rekod yang dijumpai.",
	   "oPaginate": {
		   "sFirst":        "Pertama",
		   "sPrevious":     "Sebelum",
		   "sNext":         "Kemudian",
		   "sLast":         "Akhir"
	   },
	   "oAria": {
		   "sSortAscending":  ": diaktifkan kepada susunan lajur menaik",
		   "sSortDescending": ": diaktifkan kepada susunan lajur menurun"
	   }
	},
	"iDisplayLength": 5
};

table.dataTable(settings);

// Table for requesters
var table2 = $('#table-requester');

var settings2 = {
	"processing": true,
	"serverSide": true,
	"deferRender": true,
	"ajax": "{{ route('formw.form.requester', $formw->id) }}",
	"columns": [
		{ data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
			return meta.row + meta.settings._iDisplayStart + 1;
		}},
		{ data: "name", name: "name"},
		{ data: "designation.name", name: "designation.name"},
		{ data: "action", name: "action", orderable: false, searchable: false},
	],
	"columnDefs": [
		{ className: "nowrap", "targets": [ 3 ] }
	],
	"sDom": "<t><'row'<p i>>",
	"destroy": true,
	"scrollCollapse": true,
	"oLanguage": {
		"sEmptyTable":      "Tiada data",
		"sInfo":            "Paparan dari _START_ hingga _END_ dari _TOTAL_ rekod",
		"sInfoEmpty":       "Paparan 0 hingga 0 dari 0 rekod",
		"sInfoFiltered":    "(Ditapis dari jumlah _MAX_ rekod)",
		"sInfoPostFix":     "",
		"sInfoThousands":   ",",
		"sLengthMenu":      "Papar _MENU_ rekod",
		"sLoadingRecords":  "Diproses...",
		"sProcessing":      "Sedang diproses...",
		"sSearch":          "Carian:",
	   "sZeroRecords":      "Tiada padanan rekod yang dijumpai.",
	   "oPaginate": {
		   "sFirst":        "Pertama",
		   "sPrevious":     "Sebelum",
		   "sNext":         "Kemudian",
		   "sLast":         "Akhir"
	   },
	   "oAria": {
		   "sSortAscending":  ": diaktifkan kepada susunan lajur menaik",
		   "sSortDescending": ": diaktifkan kepada susunan lajur menurun"
	   }
	},
	"iDisplayLength": 5
};

table2.dataTable(settings2);

function submit() {

	if(!$('#verified').prop('checked')) {
		swal('Harap Maaf!', 'Sila sahkan bahawa maklumat yang diberikan adalah benar.', 'error');
		$('a[data-target="#tab1"]').tab('show');
		$("body, html").animate({
		    scrollTop: $(document).height()
		}, 400)
		return;
	}

    swal({
        title: "Teruskan?",
        text: "Adakah anda pasti untuk menghantar notis ini?",
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
                url: '{{ route("formw.form", $formw->id) }}',
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
                                location.href="{{ route('formw.list') }}";
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
            location.href="{{ route('formw.list') }}";
        }
    });
}

@if(!request()->id)
    window.history.pushState('formw', 'Borang W', '{{ fullUrl() }}/{{ $formw->id }}');
@endif

function addPurpose() {

	if($('#purpose').val() == '') {
		swal('Harap Maaf!', 'Sila isi ruangan terlebih dahulu.', 'error');
		return;
	}

	$.ajax({
		url: '{{ route('formw.form.purpose', $formw->id) }}',
		method: 'POST',
		data: {
			purpose: $('#purpose').val()
		},
		dataType: 'json',
		async: true,
		success: function(data) {
			$('#purpose').val("");
			table.api().ajax.reload(null, false);
		}
	});
}

function removePurpose(id) {
	$.ajax({
		url: '{{ route('formw.form.purpose', $formw->id) }}/'+id,
		method: 'delete',
		dataType: 'json',
		async: true,
		contentType: false,
		processData: false,
		success: function(data) {
			table.api().ajax.reload(null, false);
		}
	});
}

function addRequester() {

	if($('#requester option:selected').val() == '') {
		swal('Harap Maaf!', 'Sila isi ruangan terlebih dahulu.', 'error');
		return;
	}

	$.ajax({
		url: '{{ route('formw.form.requester', $formw->id) }}',
		method: 'POST',
		data: {
			officer_id: $('#requester option:selected').val()
		},
		dataType: 'json',
		async: true,
		success: function(data) {
			table2.api().ajax.reload(null, false);
			$('#requester').val('').trigger('change');
		},
		error: function(xhr, ajaxOptions, thrownError){
	        swal('Harap Maaf!', 'Pemohon ini sudah dipilih.', 'error');
		}
	});
}

function removeRequester(id) {

	$.ajax({
		url: '{{ route('formw.form.requester', $formw->id) }}/'+id,
		method: 'delete',
		dataType: 'json',
		async: true,
		contentType: false,
		processData: false,
		success: function(data) {
			table2.api().ajax.reload(null, false);
			$('#requester').val('').trigger('change');
		}
	});
}
</script>
@endpush
