<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white p-t-15 p-b-15">
    <!-- START card -->
    <div class="card card-transparent">
        <div class="card-block">
        	<div class="row">
                @foreach($sectors as $index => $sector)
        		<div class="col-md-12 m-b-20">
                    <canvas id="myChart{{ $index }}" width="400" height="200"></canvas>
        		</div>
                @endforeach
        	</div>
        </div>
    </div>
    <!-- END card -->
</div>
<!-- END CONTAINER FLUID -->

<script type="text/javascript">
@foreach($sectors as $indSector => $sector)
var ctx{{ $indSector }} = document.getElementById("myChart{{ $indSector }}").getContext('2d');
var myChart{{ $indSector }} = new Chart(ctx{{ $indSector }}, {
    type: 'line',
    data: {
        labels: [
            @foreach(range($start_year, $end_year) as $index => $year)
                "{{ $year }}",
            @endforeach
        ],
        datasets: [
            {
                label: 'Lelaki',
                data: [
                    @foreach(range($start_year, $end_year) as $index => $year)
                        <?php
                            $report = (clone $view_report)->where('year', $year)->where('sector', $indSector + 1);
                        ?>
                        {{ $report->sum('male') }},
                    @endforeach
                ],
                backgroundColor: 'rgba(10,79,99,0.5)',
                borderColor: 'rgba(10,79,99,0.5)',
                borderWidth: 2,
            },
            {
                label: 'Perempuan',
                data: [
                    @foreach(range($start_year, $end_year) as $index => $year)
                        <?php
                            $report = (clone $view_report)->where('year', $year)->where('sector', $indSector + 1);
                        ?>
                        {{ $report->sum('female') }},
                    @endforeach
                ],
                backgroundColor: 'rgba(241,108,32,0.5)',
                borderColor: 'rgba(241,108,32,0.5)',
                borderWidth: 2,
            }
        ]
    },
    options: {
        title: {
            display: true,
            text: '{{ $name }}: {{ $sector }}',
            fontSize: 16,
        },
        legend: {
            display: true,
        },
        scales: {
            yAxes: [{
                scaleLabel: {
                    display: false,
                    labelString: 'Jumlah',
                },
                ticks: {
                    beginAtZero: true,
                    autoSkip: false
                }
            }],
            xAxes: [{
                stacked: false,
                beginAtZero: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Tahun',
                },
                ticks: {
                    stepSize: 1,
                    min: 0,
                    autoSkip: false,
                }
            }]
        }
    }
});
@endforeach
</script>
