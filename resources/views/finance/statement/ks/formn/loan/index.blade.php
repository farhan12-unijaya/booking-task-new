<div class="form-group row">
	<label>Pinjaman daripada (nyatakan)</label>

	<table class="table table-hover " id="table-loan">
		<thead>
			<tr>
				<th class="fit">Bil.</th>
				<th>Pinjaman</th>
				<th>RM sen</th>
				<th class="fit">Tindakan</th>
			</tr>
		</thead>
	</table>

    <button onclick="addLoan()" class="btn btn-primary btn-sm btn-cons" type="button"><i class="fa fa-plus m-r-5"></i> Tambah Pinjaman</button>
</div>
@push('modal')
<div class="modal fade" id="modal-addLoan" tabindex="-1" role="dialog" aria-hidden="false">
	<div class="modal-dialog ">
		<div class="modal-content-wrapper">
			<div class="modal-content">
				<div class="modal-header clearfix text-left">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
					</button>
					<h5>Maklumat <span class="semi-bold">Pinjaman daripada</span></h5>
				</div>
				<div class="modal-body">
					<form id="form-add-loan" role="form" method="post" action="{{ route('formn.loan', $statement->id) }}">
						<div class="form-group-attached">
							<div class="row">
								<div class="col-md-12">
									@include('components.input', [
										'name' => 'name',
										'label' => 'Pinjaman daripada',
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
							<button type="button" class="btn btn-info m-t-5 pull-right" onclick="submitForm('form-add-loan')"><i class="fa fa-check mr-1"></i> Simpan</button>
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
var table2 = $('#table-loan');

var settings2 = {
    "processing": true,
    "serverSide": true,
    "deferRender": true,
    "ajax": "{{ route('formn.loan', $statement->id) }}",
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

table2.dataTable(settings2);

function addLoan() {
    $('#modal-addLoan').modal('show');
    $('.modal form').trigger("reset");
    $('.modal form').validate();
}

function editLoan(id) {
    $("#modal-div").load("{{ route('formn.loan', $statement->id) }}/"+id);
}

$("#form-add-loan").submit(function(e) {
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
            $("#modal-addLoan").modal("hide");
            table2.api().ajax.reload(null, false);
        }
    });
});

function removeLoan(id) {
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
                url: '{{ route('formn.loan', $statement->id) }}/'+id,
                method: 'delete',
                dataType: 'json',
                async: true,
                contentType: false,
                processData: false,
                success: function(data) {
                    swal(data.title, data.message, data.status);
                    table2.api().ajax.reload(null, false);
                }
            });
        }
    });
}
</script>
@endpush