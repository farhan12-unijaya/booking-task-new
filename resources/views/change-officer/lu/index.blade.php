@extends('layouts.app')

@section('content')
@include('components.msg-disconnected')
<!-- START JUMBOTRON -->
<div class="jumbotron" data-pages="parallax">
	<div class=" container-fluid container-fixed-lg sm-p-l-0 sm-p-r-0">
		<div class="inner">
			<!-- START BREADCRUMB -->
			{{ Breadcrumbs::render('formlu') }}
			<div class="row">
				<div class="col-xl-12 col-lg-12 ">
					<!-- START card -->
					<div class="card card-transparent">
						<div class="card-block p-t-0">
							<h3 class='m-t-0'>Pendaftaran Perubahan <span class="semi-bold"> Pegawai / Pekerja Kesatuan Sekerja</span></h3>
							<p class="small hint-text m-t-5">
								Sila lengkapkan semua maklumat berikut mengikut turutan dan arahan yang dipaparkan.
							</p>
							<br>
							<div class="row">
								<div class="col-md-4 col-sm-6">
                                    <div class="form-group form-group-default form-group-default-select2 required" aria-required="true">
                                        <label>Tempoh Pemilihan</label>
                                        <select id="tenure_id" name="tenure_id" class="full-width autoscroll" data-init-plugin="select2" required>
                                            <option disabled hidden selected>Pilih satu</option>
                                            @foreach($tenures as $tenure)
                                            <option value="{{ $tenure->id }}">{{ $tenure->start_year }} - {{ $tenure->end_year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if($formlu->tenure->entity->branches)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group form-group-default form-group-default-select2 required" aria-required="true">
                                        <label>Cawangan / Induk Kesatuan</label>
                                        <select id="branch_id" name="branch_id" class="full-width autoscroll" data-init-plugin="select2" required>
                                            <option value="-1" selected>Induk - {{ $formlu->tenure->entity->name }}</option>
                                            @foreach($formlu->tenure->entity->branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
							</div>
						</div>
					</div>
					<!-- END card -->
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END JUMBOTRON -->
@include('components.msg-connecting')
<div class="main container-fluid container-fixed-lg">
	<div class="alert alert-danger" role="alert">
		<strong>Makluman!</strong> Kesatuan Sekerja perlu mendaftar Borang LU penggal lama terlebih dahulu atau hubungi admin untuk pemfailkan borang LU baru.
	</div>

	<div class="row">

		<div class="col-lg-12">
            @component('components.block', [
                    'percentage' => (3 - count($error_list['l']))/3*100,
                    'title' => 'Borang L',
                    'description' => ' - Notis Perubahan Pegawai-Pegawai Atau Titel Pegawai-Pegawai',
                ])
                <div class="btn-group-custom">
                    <a href="{{ route('formlu.forml', $formlu->id) }}" class="btn btn-primary pull-right btn-input"><i class="fa fa-edit mr-1"></i> Isi Borang</a>
                    @if(!count($error_list['l']))
                    <a href="{{ route('download.formlu.forml', $formlu->id) }}" target="_blank" class="btn btn-default mr-2 pull-right btn-lock"><i class="fa fa-print mr-1"></i> Cetak</a>
                    <a href="javascript:;" class="btn btn-master mr-2 pull-right btn-unlock"><i class="fa fa-lock"></i></a>
                    @endif
                </div>
            @endcomponent
        </div>
		<div class="col-lg-12">
            @component('components.block', [
                    'percentage' => (4 - count($error_list['u']))/4*100,
                    'title' => 'Borang U',
                    'description' => ' - Penyata Keputusan Undi',
                ])
                <div class="btn-group-custom">
                    <a href="{{ route('formlu.formu', $formlu->id) }}" class="btn btn-primary pull-right btn-input"><i class="fa fa-edit mr-1"></i> Isi Borang</a>
                    @if(!count($error_list['u']))
                    <a href="{{ route('download.formlu.formu', $formlu->id) }}" target="_blank" class="btn btn-default mr-2 pull-right btn-lock"><i class="fa fa-print mr-1"></i> Cetak</a>
                    <a href="javascript:;" class="btn btn-master mr-2 pull-right btn-unlock"><i class="fa fa-lock"></i></a>
                    @endif
                </div>
            @endcomponent
        </div>

		<div class="col-lg-12">
            @component('components.block', [
                    'title' => 'Borang Praecipe',
                    'disabled' => true
                ])
                <div class="btn-group-custom">
                    <a href="{{ route('formlu.praecipe', $formlu->id) }}" target="_blank" class="btn btn-default mr-2 pull-right"><i class="fa fa-print mr-1"></i> Cetak</a>
                </div>
            @endcomponent
        </div>
        @if($formlu->created_by_user_id == auth()->id())
		<div class="col-lg-12 mb-3">
			<button onclick="submit()" class="btn btn-info pull-right btn-send-custom"><i class="fa fa-check mr-1"></i> Hantar</button>
		</div>
        @endif
	</div>
</div>
@endsection
@push('js')
<script type="text/javascript">

    @if(auth()->user()->entity->tenures->count() > 1)
        <?php
            $tenure = auth()->user()->entity->tenures->last();
            $previous_tenure = auth()->user()->entity->tenures()->where('id', '<', $tenure->id)->orderBy('start_year', 'desc')->first();
        ?>
        @if($previous_tenure->formlu()->where('filing_status_id', 9)->count() == 0)
            swal('Harap Maaf!', 'Sila mendaftar borang LU Penggal lama terlebih dahulu.', 'warning');
        @endif
    @endif
	
	printed = 0;

    $('.btn-lock').on('click', function() {
        printed++;
    });

    $('.btn-unlock').on('click', function() {
        printed--;
    });

    @if(!request()->id)
        window.history.pushState('formlu', 'Borang LU', '{{ fullUrl() }}/{{ $formlu->id }}');
    @endif

    function submit() {

        if(printed != 2) {
            swal('Harap Maaf!', 'Sila cetak dokumen-dokumen di atas terlebih dahulu.', 'error');
            return;
        }

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
                    url: '{{ route("formlu.form", $formlu->id) }}',
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
                                    location.href="{{ route('formlu.list') }}";
                                }
                            });
                        }
                    }
                });
            }
        });
    }

    $(document).ready(function(){
        var socket = io('{{ env('SOCKET_HOST', '127.0.0.1') }}:{{ env('SOCKET_PORT', 3000) }}');

        socket.on('connect', function() {
            $(".msg-disconnected").slideUp();
            $(".msg-connecting").slideUp();
        });

        socket.on('disconnect', function() {
            $(".msg-disconnected").slideDown();
            $("html, body").animate({ scrollTop: 0 }, 500);
        });

        $('input, select, textarea').on('change', function() {
            socket.emit('formlu', {
                id: {{ $formlu->id }},
                name: $(this).attr('name'),
                value: $(this).attr('type') == 'checkbox' ? ($(this).prop('checked') ? 1 : 0) : $(this).val(),
                user: '{{ Cookie::get('api_token') }}'
            });
            console.log('changed');
        });

        @if($formlu->tenure_id)
            $("#tenure_id").val({{ $formlu->tenure_id }}).trigger('change');
        @endif

        @if($formlu->branch_id)
            $("#branch_id").val({{ $formlu->branch_id }}).trigger('change');
        @endif
    });
	   
</script>
@endpush