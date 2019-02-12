<!-- Modal -->
<div class="modal fade" id="modal-show" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle"><span class="bold">Gueslist</span></h5>
                <small class="text-muted">Sila isi maklumat pada ruangan di bawah.</small>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
		<div class="modal-body m-t-20">
			<form>


            <div>
                                
            <div class="card card-transparent">
		<div class="card-header px-0 search-on-button">
			<div class="pull-right">
				
			</div>
			<div class="clearfix"></div>
		</div>
		
			<table class="table table-hover " id="table-general">
				<thead>
					<tr>
						
                        <th>Nama</th>
                        <th>Email</th>
                        <th>RSVP</th>
						<th>Remainder</th>
                        <th>Hadir</th>
					</tr>
				</thead>
				
				@foreach($guestlist as $guest)
				<tbody>
				<tr>
				<td> {{$guest->name}} </td> 
				<td> {{$guest->email}} </td>
				<td> {{$guest->rsvp}} </td>
				<td> {{$guest->reminder}} </td> 
				<td> {{$guest->attend}}</td>
				</tr>
				</tbody>
				@endforeach
			</table>
		
	</div>

				


					<!-- <button type="button" class="btn pull-left btn-info"><i class="fa fa-check m-r-5"></i> Upload excel</button>
                    <button type="button" class="btn pull-right btn-info" onclick="submitForm('form-add-general')"><i class="fa fa-check m-r-5"></i> Hantar</button> -->
	            </div>
		</div>	
			</form>
			<div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
               
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">

$('#modal-show').modal('show');
$(".modal form").validate();


</script>