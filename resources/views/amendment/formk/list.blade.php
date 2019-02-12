@extends('layouts.app')
@include('plugins.datatables')

@section('content')
<!-- START JUMBOTRON -->
<div class="jumbotron" data-pages="parallax">
    <div class=" container-fluid container-fixed-lg sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <!-- START BREADCRUMB -->
            {{ Breadcrumbs::render('formk') }}
            <!-- END BREADCRUMB -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 ">
                    <!-- START card -->
                    <div class="card card-transparent">
                        <div class="card-block p-t-0">
                            <h3 class='m-t-0'>Senarai <span class="semi-bold">Borang K</span></h3>
                            <p class="small hint-text m-t-5">
                                Pengurusan permohonan Borang K boleh dilakukan melalui jadual di bawah.
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

<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
    <!-- START card -->
    <div class="card card-transparent">
        <div class="card-header px-0">
            <div class="card-title">
                @if(auth()->user()->hasAnyRole(['ks']))
                    @if(auth()->user()->entity->tenures->count() > 1)
                        <?php
                            $tenure = auth()->user()->entity->tenures->last();
                        ?>
                        @if($tenure->formlu()->where('filing_status_id', 9)->count() == 0)
                            <button onclick="swal('Harap Maaf!', 'Sila mendaftar borang LU bagi penggal ini terlebih dahulu atau hubungi admin.', 'error')" class="btn btn-success btn-cons"><i class="fa fa-plus m-r-5"></i> Borang K</button>
                        @else
                            <button onclick="location.href='{{ route('formk') }}'" class="btn btn-success btn-cons"><i class="fa fa-plus m-r-5"></i> Borang K</button>
                        @endif
                    @else
                        <button onclick="location.href='{{ route('formk') }}'" class="btn btn-success btn-cons"><i class="fa fa-plus m-r-5"></i> Borang K</button>
                    @endif
                @endif
            </div>
            <div class="pull-right">
                <div class="col-xs-12">
                    <input type="text" id="search-table" class="form-control search-table pull-right" placeholder="Carian">
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="card-block table-responsive">
            <table class="table table-hover" id="table">
                <thead>
                    <tr>
                        <th class="fit">Bil.</th>
                        <!-- <th>Nama Kesatuan</th> -->
                        <th>Nama Kesatuan</th>
                        <th>Tarikh Permohonan</th>
                        <th>Status</th>
                        <th>Surat / Lampiran</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- END card -->
</div>
<!-- END CONTAINER FLUID -->
@endsection
@push('modal')
<div class="modal fade slide-up show" id="modal-view" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <div class="modal-header clearfix text-left" style="background-color: #f3f3f3;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14"></i>
                    </button>
                    <h5>Maklumat <span class="semi-bold">Permohonan Kesatuan Sekerja</span></h5>
                    <p class="p-b-10">Semua maklumat berkenaan Pendaftaran tersebut telah dipaparkan dalam bentuk kronologi dibawah</p>

                    <div class="pb-3">
                        Nama Kesatuan: <a onclick="openModalKS()" href="javascript:;" class="text-complete bold">Kesatuan Unijaya</a ></span><br>
                        Tarikh Penubuhan: <strong>19/01/2018</strong><br>
                        Nama Setiausaha: <strong>Adlan Arif Zakaria</strong>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    @include('sample.timeline.b')
                </div>
                <div class="modal-footer" style="background-color: #f3f3f3;">
                    <button type="button" class="btn btn-secondary mt-4" data-dismiss="modal">Tutup</button>
                    @if(auth()->user()->hasAnyRole([ 'ks', 'ppw', 'pphq']))
                    <a href="{{ route('formk') }}" class="btn btn-primary mt-4"><i class="fa fa-edit mr-1 text-capitalize"></i> Kemaskini Borang</a>
                    @endif
                    @if(auth()->user()->hasAnyRole([ 'ptw','pthq']))
                    <button onclick="acceptData(1)" class="btn btn-info mt-4"><i class="fa fa-check mr-1 text-capitalize"></i> Terima Dokumen Fizikal</button>
                    @endif
                    @if(auth()->user()->hasRole('pthq'))
                    <button onclick="acceptBookData(1)" class="btn btn-info mt-4"><i class="fa fa-check mr-1 text-capitalize"></i> Terima Buku Peraturan</button>
                    @endif
                    @if(auth()->user()->hasRole('pthq'))
                    <button onclick="statusData(1)" class="btn btn-primary mt-4"><i class="fa fa-edit mr-1 text-capitalize"></i> Kemaskini Status</button>
                    @endif
                    @if(auth()->user()->hasAnyRole(['ppw','pw', 'pphq', 'pkpg', 'pkpp', 'kpks']))
                    <button onclick="location.href='{{ route('formk.query') }}'" class="btn btn-warning mt-4"><i class="fa fa-question mr-1"></i> Kuiri</button>
                    @endif
                    @if(auth()->user()->hasAnyRole(['ppw', 'pw','pphq', 'pkpp', 'pkpg']))
                    <button onclick="commentData(1)" class="btn btn-warning mt-4"><i class="fa fa-comment mr-1"></i> Ulasan/Syor</button>
                    @endif
                    @if(auth()->user()->hasRole('kpks'))
                    <button onclick="processData(1)" class="btn btn-success mt-4"><i class="fa fa-spinner mr-1"></i> Proses</button>
                    @endif
                    @if(auth()->user()->hasRole('pkpp'))
                    <button onclick="processBookData(1)" class="btn btn-success mt-4"><i class="fa fa-spinner mr-1"></i> Proses Buku Peraturan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
@push('js')
<script>
var table = $('#table');

var settings = {
    "processing": true,
    "serverSide": true,
    "deferRender": true,
    "ajax": "{{ fullUrl() }}",
    "columns": [
        { data: 'index', defaultContent: '', orderable: false, searchable: false, render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: "tenure.entity.name", name: "tenure.entity.name"},
        { data: "applied_at", name: "applied_at"},
        { data: "status.name", name: "status.name", render: function(data, type, row){
            return $("<div/>").html(data).text();
        }},
        { data: "letter", name: "letter", searchable: false, render: function(data, type, row){
            return $("<div/>").html(data).text();
        }},
        { data: "action", name: "action", orderable: false, searchable: false},
    ],
    "columnDefs": [
        { className: "nowrap", "targets": [ 5 ] }
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
    "iDisplayLength": -1
};

table.dataTable(settings);

// search box for table
$('#search-table').keyup(function() {
    table.fnFilter($(this).val());
});

$(document).ready(function() {
    $('.unapproved').click( function() {
        if($(this).parents('.col-md-8').find('textarea').hasClass('hidden')) {
            $(this).parents('.col-md-8').find('textarea').removeClass('hidden');
        }
        else {
            $(this).parents('.col-md-8').find('textarea').removeClass('hidden');
        }

    });

    $('.approved').click( function() {
        if($(this).parents('.col-md-8').find('textarea').hasClass('hidden')) {
            exit();
        }
        else {
            $(this).parents('.col-md-8').find('textarea').addClass('hidden');
        }

    });
});

// function submitData(id) {
//     swal("Berjaya", "Keputusan telah dihantar. Pegawai Tadbir HQ akan dimaklumkan melalui emel.", "success");
//     $("#modal-process").modal('hide');
// }

function viewData(id) {
    $("#modal-view").modal("show");
}

function process(id) {
    $("#modal-div").load("{{ route('formk') }}/"+id+"/process/result");
}

function receive(id) {
    $("#modal-div").load("{{ route('formk') }}/"+id+"/process/document-receive");
}

function query(id) {
    $("#modal-div").load("{{ route('formk') }}/"+id+"/process/query");
}

function recommend(id) {
    $("#modal-div").load("{{ route('formk') }}/"+id+"/process/recommend");
}

function status(id) {
    $("#modal-div").load("{{ route('formk') }}/"+id+"/process/status");
}

function processBookData(id) {
    swal({
        title: "Proses Buku Peraturan",
        text: "Sila pilih keputusan terhadap buku peraturan",
        icon: "info",
        buttons: {
            cancel: "Tutup",
            accept: {
                text: "Lengkap",
                value: "accept",
                closeModal: false,
                className: "btn-success",
            },
            reject: {
                text: "Tidak Lengkap",
                value: "reject",
                closeModal: true,
                className: "btn-danger",
            },
        }
    })
    .then((data) => {
        console.log(data);
        if (data == "accept") {
            swal({
                title: "Buku Peraturan Lengkap",
                text: "Sila isi ruangan dibawah untuk ulasan keputusan",
                icon: "info",
                buttons: ["Tutup", { text: "Hantar", closeModal: false, className: "btn-info" }],
                content: {
                    element: "textarea",
                    attributes: {
                        placeholder: "Sebab / Ulasan",
                    },
                }
            })
            .then((data) => {
                console.log(data);
                if (data !== null)
                    swal("Berjaya", "Notifikasi keputusan akan dihantar kepada KS, PWN dan HQ.", "success");
            });
        }
        else if (data == "reject") {
            rejectBook();
        }
    });
}

function acceptBookData(id) {
    swal({
        title: "Terima Buku Peraturan?",
        text: "Jika Buku Peraturan telah diterima, sila isikan tarikh penerimaan di ruangan berikut",
        icon: "info",
        buttons: ["Tutup", { text: "Hantar", closeModal: false, className: "btn-info" }],
        content: {
            element: "input",
            attributes: {
                placeholder: "Tarikh Penerimaan",
                type: "text",
                value: "{{ date('d/m/Y') }}"
            },
        }
    })
    .then((data) => {
        console.log(data);
        if (data !== null)
            swal("Berjaya", "Buku Peraturan telah diterima. Kesatuan Sekerja akan dimaklumkan melalui emel.", "success");
    });
}

</script>
@endpush
