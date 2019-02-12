<?php

namespace App\Http\Controllers;
use App\LogModel\LogSystem;
use App\MasterModel\MasterState;
use App\OtherModel\Inbox;
use App\GuestList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Booking;
use App\MasterRoom;
use Auth;
use Excel;

class BookingController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index(Request $request) {

       $book_category = array('id' => '0' , 'name' => 'Dewan' );
        $list = range(1,16);
        array_push($list, 1);
     	$districts = MasterState::whereIn('id', $list)->get();
     	$room = MasterRoom::All();
     	dd($room);
    	return view('booking.index', compact('book_category','districts'));
    }



    public function newbooking(Request $request){
// dd($request->all());
			// $validator = Validator::make($request->all(), [
   //          'kategori' => 'required|integer',
   //          'idroom' => 'required|integer',
   //          'from_date' => 'required|date|after:tomorrow',
			// 'from_time' => 'date_format:"H:i"|required|before:'.$this->get('to_time'),
			// 'to_date' => 'date_format:d/m/Y|after:start_date',
   //          'to_time' => 'required',
   //      ]);

   //      if ($validator->fails()) {
   //          // If validation failed
   //          return response()->json(['errors' => $validator->errors()], 422);
   //      }

	        $booking = new Booking();
            
            $booking->from_date = date("Y-m-d", strtotime($request->from_date));
            $booking->room_id = $request->idroom;
            $booking->from_time = date("H:i", strtotime($request->from_time));
            $booking->to_date = date("Y-m-d", strtotime($request->end_date));
            $booking->to_time = date("H:i", strtotime($request->to_time));
            $booking->pemohon_id =1;
            $booking->keterangan = $request->keterangan;
  
        	$booking->save();

         return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => ' Telah berjaya dibooking']);
        // return $booking;

    }

    public function Uploadguestlist(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'nama_file' => 'required',
      ]);

      if ($validator->passes()) {
        $input = $request->all();
        $file = $input['nama_file'];
		 Excel::load($file, function($reader) {
        $results = $reader->get();
        


        foreach ($results as $key => $value) {
       
       	$post = new GuestList();
       	$post->nama = $value['nama'];
       	$post->email = $value['email'];
       	$post->booking_id = 1;
       	$post->keterangan = $value['keterangan'];
       	$post->save();


        }
    		
    		});

                 return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => ' Telah berjaya dibooking']);


      }

      return response()->json(['error'=>$validator->errors()->all()]);
    }

	function csvToArray($filename = '', $delimiter = ',')
	{
	    if (!file_exists($filename) || !is_readable($filename))
	        return false;

	    $header = null;
	    $data = array();
	    if (($handle = fopen($filename, 'r')) !== false)
	    {
	        while (($row = fgetcsv($handle, 10000, $delimiter)) !== false)
	        {
	            if (!$header)
	                $header = $row;
	            else
	                $data[] = array_combine($header, $row);
	        }
	        fclose($handle);
	    }

	    return $data;
	}


    public function view(Request $request){
        
        $inbox = Inbox::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 5;
        $log->activity_type_id = 2;
        $log->description = "Buka paparan Inbox";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $inbox->inbox_status_id = 3;
        $inbox->save();

        return view('inbox.view', compact('inbox'));
    }
}
