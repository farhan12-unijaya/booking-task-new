@extends('layouts.app')
@include('plugins.datatables')
@include('plugins.wizard')

@section('content')
<!-- START JUMBOTRON -->
<div class="jumbotron m-b-0" data-pages="parallax">
    <div class=" container-fluid container-fixed-lg sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <!-- START BREADCRUMB -->
            {{ Breadcrumbs::render('guestlist') }}
            <!-- END BREADCRUMB -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 ">
                    <!-- START card -->
                    <div class="card card-transparent">
                        <div class="card-block p-t-0">
                            <h3 class='m-t-0'>Guestlist</h3>
                            <p class="small hint-text m-t-5">
                                Pengaturan Guestlist boleh dilakukan melalui jaduan di bawah.
                            </p>
                        </div>
                    </div>
                    <!-- END card -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END JUMBOTRON -->

<div id="rootwizard">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist" {{-- data-init-reponsive-tabs="dropdownfx" --}} >
		<li class="nav-item ml-md-3">
			<a class="active" data-toggle="tab" href="#" data-target="#tab1" role="tab"> <span></span></a>
		</li>
		
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div class="tab-pane active slide-right" id="tab1">
			<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
	<!-- START card -->
	<div class="card card-transparent mb-0 mt-3">
		<form class="p-b-10" id="form-general" role="form" autocomplete="off" method="post" action="{{ route('admin.holiday.general') }}" novalidate>
			<div class="form-group-attached">
				<div class="row clearfix filter">
					<div class="col-md-6">
						<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
							<label>Kategori</label>
							<select name="holiday_type_id" id="holiday_type_id" class="full-width autoscroll" data-init-plugin="select2" required="">
								<option selected value="-1">-- Semua Kategori --</option>
								@foreach($room_category as $type)
									<option value="{{ $type->id }}">{{ $type->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
							<label>Dari</label>
							@include('components.date', [
	                    'name' => 'start_date',
	                    'label' => ''
	                ])
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
							<label>Sampai</label>
							@include('components.date', [
	                    'name' => 'end_date',
	                    'label' => ''
	                ])
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
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
						<th>Jam Selesai</th>
                        <th>Guestlist</th>
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



@push('js')
<script>
var table = $('#table-general');

var settings = {
	"processing": true,
	"serverSide": true,
	"deferRender": true,
	"ajax": "{{ route('guestlist') }}",
	"columns": [
		{ data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
			return meta.row + meta.settings._iDisplayStart + 1;
		}},
		{ data: "ruangan", name: "ruangan"},
        { data: "tanggal", name: "tanggal"},
        { data: "time_from", name: "time_from"},
		{ data: "time_to", name: "time_to"},
        { data: "action", name: "action", orderable: false, searchable: false},
	],
	"columnDefs": [
		{ className: "nowrap", "targets": [ 5 ] }
	],
    "sDom": "B<t><'row'<p i>>",
    "buttons": [
       
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
			{ data: "kategori", name: "kategori"},
			{ data: "nama", name: "nama"},
			{ data: "gambar", name: "gambar"},
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



function show(id) {

	
	$("#modal-div").load("{{ route('guestlist') }}/"+id);
}

function add(id) {

	console.log('ini id addnya', id);
	var test = id;

	$("#modal-div").load("{{route('guestlist.add.form','"+id+"')}}");
}

function upload(id) {

$("#modal-div").load("{{ route('guestlist.upload','" +id+"' )}}");
}


$("#form-add-general").submit(function(e) {
	e.preventDefault();
	var form = $(this);
	console.log(form, 'beza');

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
				url: '{{ route('booking') }}/'+id,
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

		</div>
      
		
	</div>
</div>
@endsection
