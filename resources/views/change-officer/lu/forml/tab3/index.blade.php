<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
	<!-- START card -->
	<div class="card card-transparent">
		<div class="card-header px-0">
			<div class="pull-right">
				<div class="col-xs-12">
					<input type="text" id="officer-search-table" class="form-control search-table pull-right" placeholder="Carian">
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="card-block">
			<table class="table table-hover " id="table-officer">
				<thead>
					<tr>
						<th class="fit">Bil.</th>
						<th>Jawatan</th>
						<th>Nama Pegawai</th>
						<th class="fit">Tarikh Memegang Jawatan</th>
						<th class="fit">Tindakan</th>
					</tr>
				</thead>
			</table>
			<div class="card-title p-t-10">
				<button id="" class="btn btn-primary btn-cons" type="button" onclick="addOfficer()"><i class="fa fa-plus m-r-5"></i> Tambah Pegawai</button>
			</div>
		</div>
	</div>
	<!-- END card -->

	<div class="row mt-5">
		<div class="col-md-12">
			<ul class="pager wizard no-style">
				<li class="submit">
					<button onclick="save()" class="btn btn-info btn-cons btn-animated from-left pull-right fa fa-check" type="button">
						<span>Simpan</span>
					</button>
				</li>
				<li class="next">
					<button class="btn btn-success btn-cons btn-animated from-left pull-right fa fa-angle-right" type="button">
						<span>Seterusnya</span>
					</button>
				</li>
				<li class="previous">
					<button class="btn btn-default btn-cons btn-animated from-left fa fa-angle-left" type="button">
						<span>Kembali</span>
					</button>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END CONTAINER FLUID -->
@push('modal')
<div class="modal fade" id="modal-addOfficer" tabindex="-1" role="dialog" aria-hidden="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content-wrapper">
			<div class="modal-content">
				<div class="modal-header clearfix text-left">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
					</button>
					<h5>Maklumat <span class="semi-bold">Pegawai</span></h5>
					<p class="p-b-10">Maklumat pegawai yang memegang jawatan.</p>
				</div>
				<div class="modal-body">
					<form id="form-add-officer" class="is-icpassport" role="form" method="post" action="{{ route('formlu.officer', $formlu->id) }}">
						<p class="m-t-10 bold">Maklumat Pegawai</p>
						<div class="form-group-attached">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
										<label>Jawatan</label>
										<select name="designation_id" class="full-width autoscroll" data-init-plugin="select2" required>
											<option value="" selected="" disabled="">Pilih satu..</option>
											@foreach($designations as $designation)
											<option value="{{ $designation->id }}"
												>{{ $designation->name }}
											</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@include('components.input', [
										'name' => 'name',
										'label' => 'Nama Pegawai',
										'mode' => 'required',
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@component('components.label', [
										'name' => 'nationality_country_id',
										'label' => 'Jenis Kewarganegaraan',
										'mode' => 'required',
									])
									<div class="radio radio-primary ">
										<input name="is_malaysian" value="1" id="is_malaysian_yes" class="hidden is-ic" required="" type="radio">
										<label for="is_malaysian_yes">Warganegara</label>
										<input name="is_malaysian" value="2" id="is_malaysian_no" class="hidden is-passport" required="" type="radio">
										<label for="is_malaysian_no">Bukan Warganegara</label>
									</div>
									@endcomponent
								</div>
							</div>
							<div class="row nationality hidden">
								<div class="col-md-12">
									<div class="form-group form-group-default required form-group-default-select2 form-group-default-custom required">
										<label>Negara Kewarganegaraan</label>
										<select  id="officer_nationality_country_id" name="nationality_country_id" class="full-width autoscroll select-modal" data-init-plugin="select2">
											<option disabled hidden selected>Pilih satu</option>
											@foreach($countries as $country)
												<option value="{{ $country->id }}"
													>{{ $country->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 input-ic">
									@include('components.input', [
										'name' => 'identification_no',
										'label' => 'No. Kad Pengenalan',
										'mode' => 'required',
										'class' => 'numeric',
										'options' => 'maxlength=12 minlength=12',
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 input-passport">
									@include('components.input', [
										'name' => 'identification_no',
										'label' => 'No. Passport',
										'mode' => 'required',
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@include('components.date', [
										'name' => 'birth_date',
										'label' => 'Tarikh Lahir',
										'mode' => 'required',
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@include('components.input', [
										'name' => 'occupation',
										'label' => 'Pekerjaan',
										'mode' => 'required',
									])
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@include('components.date', [
										'name' => 'start_date',
										'label' => 'Tarikh Memegang Jawatan',
									])
								</div>
							</div>
						</div>


						<p class="m-t-10 bold">Alamat Pegawai</p>
						<div class="form-group-attached">
							@include('components.input', [
								'name' => 'address1',
								'label' => 'Alamat 1',
								'mode' => 'required',
								'info' => 'Alamat Surat Menyurat terkini',
							])

							<div class="row clearfix">
								<div class="col-md-6">
									@include('components.input', [
										'name' => 'address2',
										'label' => 'Alamat 2',
									])
								</div>
								<div class="col-md-6">
									@include('components.input', [
										'name' => 'address3',
										'label' => 'Alamat 3',
									])
								</div>
							</div>

							<div class="row clearfix address">
								<div class="col-md-4">
									<div class="form-group form-group-default required">
										<label>Poskod</label>
										<input class="form-control numeric postcode" name="postcode" aria-required="true" type="text" value="" required minlength="5" maxlength="5">
									</div>
								</div>
								<div class="col-md-4">
	                                <div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
	                                    <label><span>Negeri</span></label>
	                                    <select id="officer_state_id" name="state_id" class="full-width autoscroll state" data-init-plugin="select2" required="">
	                                        <option value="" selected="" disabled="">Pilih satu..</option>
	                                        @foreach($states as $index => $state)
	                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
	                                        @endforeach
	                                    </select>
	                                </div>
	                            </div>
	                            <div class="col-md-4">
	                                <div class="form-group form-group-default form-group-default-custom form-group-default-select2 required">
	                                    <label><span>Daerah</span></label>
	                                    <select id="officer_district_id" name="district_id" class="full-width autoscroll district" data-init-plugin="select2" required="">
	                                    </select>
	                                </div>
	                            </div>
							</div>
						</div>
						<input type="hidden" name="created_by_user_id" value="{{ auth()->id() }}"/>
					</form>
				</div>

				<div class="modal-footer">
					<div class="col-md-12 p-t-10">
						<button type="button" class="btn btn-info m-t-5 pull-right submit" onclick="submitForm('form-add-officer')"><i class="fa fa-check" ></i> Simpan</button>

					</div>
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
</div>
@endpush

@push('js')
<script>

	$("#officer_nationality_id").select2();

	function checkAge() {
	    var ic = document.getElementById("ic").value;
	    var age = getAge(ic);

	    if(age < 18) {
	    	swal("Opps!", "Umur mestilah 18 tahun dan ke atas", "error");
            e.preventDefault();
	    }
	}

	$("input[name=is_malaysian]").on('change', function() {
		if( $(this).val() == 1 )
			$(".nationality").addClass("hidden");
		else
			$(".nationality").removeClass("hidden");
	});

	var table2 = $('#table-officer');

	var settings2 = {
	    "processing": true,
	    "serverSide": true,
	    "deferRender": true,
	    "ajax": "{{ route('formlu.officer', $formlu->id) }}",
	    "columns": [
	        { data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
	            return meta.row + meta.settings._iDisplayStart + 1;
	        }},
	        { data: "designation.name", name: "designation.name", searchable: false},
	        { data: "name", name: "name"},
	        { data: "held_at", name: "held_at"},
	        { data: "action", name: "action", orderable: false, searchable: false},
	    ],
	    "columnDefs": [
	        { className: "nowrap", "targets": [ 4 ] }
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

	// search box for table
	$('#officer-search-table').keyup(function() {
	    table2.fnFilter($(this).val());
	});

	function addOfficer() {
	    $('#modal-addOfficer').modal('show');
	    $('.modal form').trigger("reset");
	    $('.modal form').validate();
	}

	function editOfficer(id) {
		$("#modal-div").load("{{ route('formlu.officer', $formlu->id) }}/"+id);
	}

	$("#form-add-officer").submit(function(e) {
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
	            $("#modal-addOfficer").modal("hide");
				if(data.count < 1) {
					$("a[data-target='#tab3'] i").removeClass('text-success');
					$("a[data-target='#tab3'] i").removeClass('fa-check');
					$("a[data-target='#tab3'] i").addClass('text-danger');
					$("a[data-target='#tab3'] i").addClass('fa-times');
				}
				else {
					$("a[data-target='#tab3'] i").removeClass('text-danger');
					$("a[data-target='#tab3'] i").removeClass('fa-times');
					$("a[data-target='#tab3'] i").addClass('text-success');
					$("a[data-target='#tab3'] i").addClass('fa-check');
				}
	            table2.api().ajax.reload(null, false);
	        }
	    });
	});

	function removeOfficer(id) {
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
	                url: '{{ route('formlu.officer', $formlu->id) }}/'+id,
	                method: 'delete',
	                dataType: 'json',
	                async: true,
	                contentType: false,
	                processData: false,
	                success: function(data) {
	                    swal(data.title, data.message, data.status);
						if(data.count < 1) {
							$("a[data-target='#tab3'] i").removeClass('text-success');
							$("a[data-target='#tab3'] i").removeClass('fa-check');
							$("a[data-target='#tab3'] i").addClass('text-danger');
							$("a[data-target='#tab3'] i").addClass('fa-times');
						}
						else {
							$("a[data-target='#tab3'] i").removeClass('text-danger');
							$("a[data-target='#tab3'] i").removeClass('fa-times');
							$("a[data-target='#tab3'] i").addClass('text-success');
							$("a[data-target='#tab3'] i").addClass('fa-check');
						}
	                    table2.api().ajax.reload(null, false);
	                }
	            });
	        }
	    });
	}

</script>
@endpush
