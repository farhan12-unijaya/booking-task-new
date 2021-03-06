<!-- START CONTAINER FLUID -->
<div class=" container-fluid container-fixed-lg bg-white">
	<!-- START card -->
	<div class="card card-transparent">
		<div class="card-header px-0">
			<div class="pull-right">
				<div class="col-xs-12">
					<input type="text" id="search-table" class="form-control pull-right" placeholder="Carian..">
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="card-block table-responsive">
			<table class="table table-hover " id="table">
				<thead>
					<tr>
						<th class="fit">Bil.</th>
						<th>Tahun</th>
						@foreach($categories as $index => $category)
						<th class="fit">{{ $category }}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					@foreach(range($start_year, $end_year) as $index => $year)
					<?php
						$report = (clone $view_report)->where('year', $year);
					?>
					<tr>
						<td>{{ $index+1 }}</td>
						<td>{{ $year }}</td>
						@foreach($categories as $ind => $category)
						<td align="center">{{ $report->where('category', $ind+1)->sum('unions') }}</td>
						@endforeach
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<!-- END card -->
</div>
<!-- END CONTAINER FLUID -->
