<div class="form-group row">
	<label>Liabiliti lain (nyatakan)</label>

	<table class="table table-hover " id="table-liability">
		<thead>
			<tr>
				<th class="fit">Bil.</th>
				<th>Liabiliti</th>
				<th>RM sen</th>
				<th class="fit">Tindakan</th>
			</tr>
		</thead>
	</table>

    <button onclick="addLiability()" class="btn btn-primary btn-sm btn-cons" type="button"><i class="fa fa-plus m-r-5"></i> Tambah Liabiliti</button>
</div>
@push('modal')
<div class="modal fade" id="modal-addLiability" tabindex="-1" role="dialog" aria-hidden="false">
	<div class="modal-dialog ">
		<div class="modal-content-wrapper">
			<div class="modal-content">
				<div class="modal-header clearfix text-left">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
					</button>
					<h5>Maklumat <span class="semi-bold">Liabiliti Lain</span></h5>
				</div>
				<div class="modal-body">
					<form id="form-add-liability" role="form" method="post" action="{{ route('formn.liability', $statement->id) }}">
						<div class="form-group-attached">
							<div class="row">
								<div class="col-md-12">
									@include('components.input', [
										'name' => 'name',
										'label' => 'Liabiliti lain',
										'mode' => 'required',
									])
								</div>
                            </div>
							<div class="row">
                                <div class="col-md-12">
									@include('components.input', [
										'name' => 'total',
										'label' => 'RM sen',
                                        'class' => 'decimal',
										'mode' => 'required',
									])
								</div>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-md-12 p-t-10">
							<button type="button" class="btn btn-info m-t-5 pull-right" onclick="submitForm('form-add-liability')"><i class="fa fa-check mr-1"></i> Simpan</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
</div>
@endpush
@push('js')
<script type="text/javascript">
var table3 = $('#table-liability');

var settings3 = {
    "processing": true,
    "serverSide": true,
    "deferRender": true,
    "ajax": "{{ route('formn.liability', $statement->id) }}",
    "columns": [
        { data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: "name", name: "name"},
        { data: "total", name: "total"},
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
    "iDisplayLength": 10
};

table3.dataTable(settings3);

function addLiability() {
    $('#modal-addLiability').modal('show');
    $('.modal form').trigger("reset");
    $('.modal form').validate();
}

function editLiability(id) {
    $("#modal-div").load("{{ route('formn.liability', $statement->id) }}/"+id);
}

$("#form-add-liability").submit(function(e) {
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
            $("#modal-addLiability").modal("hide");
            table3.api().ajax.reload(null, false);
        }
    });
});

function removeLiability(id) {
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
                url: '{{ route('formn.liability', $statement->id) }}/'+id,
                method: 'delete',
                dataType: 'json',
                async: true,
                contentType: false,
                processData: false,
                success: function(data) {
                    swal(data.title, data.message, data.status);
                    table3.api().ajax.reload(null, false);
                }
            });
        }
    });
}
</script>
@endpush