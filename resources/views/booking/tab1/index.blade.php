<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
	<!-- START card -->
	<div class="card card-transparent mb-0 mt-3">
		<form class="p-b-10" id="form-general" role="form" autocomplete="off" method="post" action="{{ route('newbooking') }}" novalidate>
			<div class="form-group-attached">
				<div class="row clearfix filter">
					<div class="col-md-4">
						<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
							<label>Kategori</label>
							<select name="holiday_type_id" id="holiday_type_id" class="full-width autoscroll" data-init-plugin="select2" required="">
								<option selected value="-1">-- Semua Kategori --</option>
								<option selected value="1">Dewan </option>
								<option selected value="2">Bilik </option>


							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
							<label>Mulai Booking</label>
							@include('components.date', [
	                    'name' => 'start_date',
	                    'label' => ''
	                ])
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
							<label>Selesai Booking</label>
							@include('components.date', [
	                    'name' => 'start_time',
	                    'label' => ''
	                ])
						</div>
					</div>
					<div class="col-md-3">
													<label>Selesai Booking</label>

						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card card-transparent">
		<div class="card-header px-0 search-on-button">
			<div class="pull-right">
				<div class="col-xs-12">
					<input type="text" id="general-search-table" class="form-control search-table pull-right" placeholder="Carian..">
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="card-block">
			<table class="table table-hover " id="table-general">
				<thead>
					<tr>
						<th class="fit">Bil.</th>
						<th>Cuti</th>
						<th class="fit">Jumlah Hari</th>
						<th class="fit">Tarikh Mula</th>
						<th class="fit">Hari</th>
						<th class="fit">Tindakan</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	<!-- END card -->
	<div class="row mt-5">
		<div class="col-md-12">
			<ul class="pager wizard no-style">
				<li class="next">
					<button class="btn btn-success btn-cons btn-animated from-left pull-right fa fa-angle-right" type="button">
						<span>Seterusnya</span>
					</button>
				</li>
				<li>
					<button class="btn btn-default btn-cons btn-animated from-left fa fa-angle-left" type="button" onclick="back()">
						<span>Kembali</span>
					</button>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END CONTAINER FLUID -->
		
@push('modal')
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

		<!-- 	<form id="form-add-general" role="form" method="post" action="{{ route('newbooking') }}">
				@csrf
 
				<div class="modal-body m-t-20">
					<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
						<label>Kategori</label>
						<select id="general_type_id" name="kategori" class="full-width autoscroll" data-init-plugin="select2" required="">
								<option selected value="1">Dewan </option>
								<option value="2">Bilik </option>



						</select>

					</div>
						@include('components.input', [
	                    'name' => 'idroom',
	                    'label' => 'Room ID',
	                    'mode' => 'required',
	                ])

					@include('components.date', [
	                    'name' => 'from_date',
	                    'label' => 'Tanggal mulai',
	                    'mode' => 'required',
	                ])
					
					@include('components.input', [
	                    'name' => 'from_time',
	                    'label' => 'Waktu mulai',
	                    'mode' => 'required',
	                    'type' => 'time',
	                ])
					
					@include('components.date', [
	                    'name' => 'to_date',
	                    'label' => 'Tanggal Akhir',
	                    'mode' => 'required',

	                ])
					
					@include('components.input', [
	                    'name' => 'to_time',
	                    'label' => 'Waktu Akhir',
	                    'mode' => 'required',
	                    'type' => 'time',
	                    'value' => '2018-06-12T19:30'

	                ])

	                @include('components.textarea', [
	                    'name' => 'keterangan',
	                    'label' => 'Keterangan',
	                    'mode' => 'required',
	                ])

					
	            </div>

			</form> -->
<form id="form-add-general" role="form" method="post" action="{{ route('newbooking') }}">
				@csrf
 
				<div class="modal-body m-t-20">
					<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
						<label>Kategori</label>
						<select id="general_type_id" name="kategori" class="full-width autoscroll" data-init-plugin="select2" required="">
								<option selected value="1">Dewan </option>
								<option value="2">Bilik </option>



						</select>

					</div>
						@include('components.input', [
	                    'name' => 'idroom',
	                    'label' => 'Room ID',
	                    'mode' => 'required',
	                ])

					@include('components.date', [
	                    'name' => 'from_date',
	                    'label' => 'Tanggal mulai',
	                    'mode' => 'required',
	                ])
					
					@include('components.input', [
	                    'name' => 'from_time',
	                    'label' => 'Waktu mulai',
	                    'mode' => 'required',
	                    'type' => 'time',
	                ])
					
					@include('components.date', [
	                    'name' => 'end_date',
	                    'label' => 'Tanggal Akhir',
	                    'mode' => 'required',

	                ])
					
					@include('components.input', [
	                    'name' => 'to_time',
	                    'label' => 'Waktu Akhir',
	                    'mode' => 'required',
	                    'type' => 'time',
	                    'value' => '2018-06-12T19:30'

	                ])

	                @include('components.textarea', [
	                    'name' => 'keterangan',
	                    'label' => 'Keterangan',
	                    'mode' => 'required',
	                ])

					<div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-info" ><i class="fa fa-check m-r-5"></i> Hantar</button>
            </div>
	            </div>

			</form>
			 <form action="{{ url('booking/uploadguestlist') }}" enctype="multipart/form-data" method="POST">
    <div class="alert alert-danger print-error-msg" style="display:none">
      <ul></ul>
    </div>

    <div class="tampil" style="display:none">
      <img src="" style="height:300px;width:500px">
    </div>

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  
      <div class="form-group">
      <label>Masukkan File:</label>
      <input type="file" name="nama_file" class="form-control">
    </div>

    <div class="form-group">
      <button class="btn btn-success upload-image" type="submit">Kirim</button>
    </div>
  </form>

        </div>
    </div>
</div>
@endpush

 

@push('js')
<script>
var table = $('#table-general');

var settings = {
	"processing": true,
	"serverSide": true,
	"deferRender": true,
	"ajax": "{{ route('booking') }}",
	"columns": [
		{ data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
			return meta.row + meta.settings._iDisplayStart + 1;
		}},
		{ data: "name", name: "name"},
		{ data: "duration", name: "duration"},
		{ data: "start_date", name: "start_date"},
		{ data: "day", name: "day"},
		{ data: "action", name: "action", orderable: false, searchable: false},
	],
	"columnDefs": [
		{ className: "nowrap", "targets": [ 5 ] }
	],
    "sDom": "B<t><'row'<p i>>",
    "buttons": [
        {
            text: '<i class="fa fa-plus m-r-5"></i> Ruangan',
            className: 'btn btn-success btn-cons',
            action: function ( e, dt, node, config ) {
                addGeneral();
            }
        },
        {
            text: '<i class="fa fa-print m-r-5"></i> Cetak',
            extend: 'print',
            className: 'btn btn-default btn-sm',
            exportOptions: {
                columns: ':visible:not(.nowrap)'
            }
        },
        {
            text: '<i class="fa fa-download m-r-5"></i> Excel',
            extend: 'excelHtml5',
            className: 'btn btn-default btn-sm',
            exportOptions: {
                columns: ':visible:not(.nowrap)'
            }
        },
        {
            text: '<i class="fa fa-download m-r-5"></i> PDF',
            extend: 'pdfHtml5',
            className: 'btn btn-default btn-sm',
            exportOptions: {
                columns: ':visible:not(.nowrap)'
            }
        },
    ],
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
	"iDisplayLength": 10
};

table.dataTable(settings);

// search box for table
$('#general-search-table').keyup(function() {
	table.fnFilter($(this).val());
});


$(".filter select").on('change', function() {
    var form = $("#form-general");

    settings = {
        "processing": true,
        "serverSide": true,
        "deferRender": true,
        "ajax" : form.attr('action')+"?"+form.serialize(),
        "columns": [
			{ data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
				return meta.row + meta.settings._iDisplayStart + 1;
			}},
			{ data: "name", name: "name"},
			{ data: "duration", name: "duration"},
			{ data: "start_date", name: "start_date"},
			{ data: "day", name: "day"},
			{ data: "action", name: "action", orderable: false, searchable: false},
		],
		"columnDefs": [
			{ className: "nowrap", "targets": [ 5 ] }
		],
	    "sDom": "B<t><'row'<p i>>",
	    "buttons": [
	        {
	            text: '<i class="fa fa-plus m-r-5"></i> Cuti Persekutuan',
	            className: 'btn btn-success btn-cons',
	            action: function ( e, dt, node, config ) {
	                addGeneral();
	            }
	        },
	        {
	            text: '<i class="fa fa-print m-r-5"></i> Cetak',
	            extend: 'print',
	            className: 'btn btn-default btn-sm',
	            exportOptions: {
	                columns: ':visible:not(.nowrap)'
	            }
	        },
	        {
	            text: '<i class="fa fa-download m-r-5"></i> Excel',
	            extend: 'excelHtml5',
	            className: 'btn btn-default btn-sm',
	            exportOptions: {
	                columns: ':visible:not(.nowrap)'
	            }
	        },
	        {
	            text: '<i class="fa fa-download m-r-5"></i> PDF',
	            extend: 'pdfHtml5',
	            className: 'btn btn-default btn-sm',
	            exportOptions: {
	                columns: ':visible:not(.nowrap)'
	            }
	        },
	    ],
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
		"iDisplayLength": 10
    };

    table.dataTable(settings);
});


function addGeneral() {
	$('#modal-addGeneral').modal('show');
	$('.modal form').trigger("reset");
	$('.modal form').validate();
}

function editGeneral(id) {
	$("#modal-div").load("{{ route('admin.holiday.general') }}/"+id);
}

$("#form-add-general").submit(function(e) {
	e.preventDefault();
	var form = $(this);

	if(!form.valid())
	   return;

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
			$("#modal-addGeneral").modal("hide");
			table.api().ajax.reload(null, false);
		}
	});
});

function removeGeneral(id) {
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
				url: '{{ route('admin.holiday.general') }}/'+id,
				method: 'delete',
				dataType: 'json',
				async: true,
				contentType: false,
				processData: false,
				success: function(data) {
					swal(data.title, data.message, data.status);
					table.api().ajax.reload(null, false);
				}
			});
		}
	});
}
</script>
@endpush