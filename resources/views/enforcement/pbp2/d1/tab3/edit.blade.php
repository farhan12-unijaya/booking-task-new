<div class="modal fade" id="modal-edit-arbitrator" tabindex="-1" role="dialog" aria-hidden="false">
	<div class="modal-dialog ">
		<div class="modal-content-wrapper">
			<div class="modal-content">
				<div class="modal-header clearfix text-left">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
					</button>
					<h5>Maklumat <span class="semi-bold">Pemeriksa</span></h5>
					<p class="p-b-10">Maklumat pemeriksa undi.</p>
				</div>
				<div class="modal-body">
					<form id="form-edit-arbitrator" role="form" method="post" action="{{ route('pbp2.d1.arbitrator.form', [request()->id, $arbitrator->id]) }}">
						<div class="form-group-attached">
							<div class="row">
								<div class="col-md-12">
									@include('components.input', [
										'name' => 'name',
										'label' => 'Nama',
										'mode' => 'required',
										'value' => $arbitrator->name
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@include('components.input', [
										'name' => 'identification_no',
										'label' => 'No. KPPN / No. Passpot',
										'mode' => 'required',
										'value' => $arbitrator->identification_no
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@include('components.date', [
										'name' => 'appointed_date',
										'label' => 'Tarikh Dilantik',
										'mode' => 'required',
										'value' => $arbitrator->appointed_at ? date('d/m/Y', strtotime($arbitrator->appointed_at)) : date('d/m/Y')
									])
								</div>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-md-12 p-t-10">
							<button type="button" class="btn btn-info m-t-5 pull-right" onclick="submitForm('form-edit-arbitrator')"><i class="fa fa-check mr-1"></i> Simpan</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
</div>

<script src="{{ asset('js/global.js') }}"></script>
<script type="text/javascript">

$('#modal-edit-arbitrator').modal('show');
$(".modal form").validate();

$("#form-edit-arbitrator").submit(function(e) {
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
            $("#modal-edit-arbitrator").modal("hide");
            table3.api().ajax.reload(null, false);
        }
    });
});
</script>