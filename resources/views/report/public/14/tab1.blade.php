<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white p-t-15 p-b-15">
    <!-- START card -->
    <div class="card card-transparent">
        <div class="card-block">
        	<div class="row">
        		<div class="col-md-12 m-b-20">
        			<canvas id="myChart" width="400" height="200"></canvas>
        		</div>
        	</div>
        </div>
    </div>
    <!-- END card -->
</div>
<!-- END CONTAINER FLUID -->

<script type="text/javascript">
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach(range($start_year, $end_year) as $index => $year)
                "{{ $year }}",
            @endforeach
        ],
        datasets: [{
            label: 'Kesatuan Sekerja',
            data: [
                @foreach(range($start_year, $end_year) as $index => $year)
                    <?php
                        $report = (clone $view_report)->where('year', $year);
                    ?>
                    {{ $report->sum('unions')}},
                @endforeach
            ],
            backgroundColor: [
                'rgba(10,79,99,0.5)',
                'rgba(241,108,32,0.5)',
                'rgba(255,99,132,0.5)',
                'rgba(255,205,86,0.5)',
                'rgba(75,192,192,0.5)',
                'rgba(83,210,20,0.5)',
                'rgba(153,51,153,0.5)',
                'rgba(255,0,0,0.5)',
                'rgba(89,89,89,0.5)',
                'rgba(136,68,0,0.5)',
            ]
        }],
        labels: [
            @foreach(range($start_year, $end_year) as $index => $year)
                '{{ $year }}',
            @endforeach
        ]
    },
    options: {
        title: {
            display: true,
            text: '{{ $name }}',
            fontSize: 16,
        },
        responsive: true
    }
});
</script>
