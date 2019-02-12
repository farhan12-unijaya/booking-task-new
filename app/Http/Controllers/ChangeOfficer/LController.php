<?php

namespace App\Http\Controllers\ChangeOfficer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ViewModel\ViewUserDistributionPTW;
use App\ViewModel\ViewUserDistributionPPW;
use App\ViewModel\ViewUserDistributionPTHQ;
use App\ViewModel\ViewUserDistributionPPHQ;
use App\Mail\Filing\Queried;
use App\Mail\Filing\Distributed;
use App\Mail\Filing\SendToHQ;
use App\Mail\Filing\Received;
use App\Mail\Filing\ReceivedHQ;
use App\Mail\FormL\ApprovedKS;
use App\Mail\FormL\ApprovedPWN;
use App\Mail\FormL\Rejected;
use App\Mail\FormL\Sent;
use App\Mail\FormL\NotReceived;
use App\FilingModel\FormL;
use App\FilingModel\FormLLeaving;
use App\FilingModel\FormLOfficer;
use App\FilingModel\Branch;
use App\FilingModel\Officer;
use App\MasterModel\MasterMeetingType;
use App\MasterModel\MasterDesignation;
use App\MasterModel\MasterState;
use App\MasterModel\MasterCountry;
use App\OtherModel\Address;
use App\LogModel\LogSystem;
use App\LogModel\LogFiling;
use App\FilingModel\Query;
use App\UserStaff;
use App\User;
use Carbon\Carbon;
use Validator;
use Mail;
use PDF;
use Storage;
use App\Custom\PhpWord;

class LController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the list of data
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request) {

        if($request->ajax()) {
            $log = new LogSystem;
            $log->module_id = 20;
            $log->activity_type_id = 1;
            $log->description = "Papar senarai Borang L";
            $log->data_old = json_encode($request->input());
            $log->url = $request->fullUrl();
            $log->method = strtoupper($request->method());
            $log->ip_address = $request->ip();
            $log->created_by_user_id = auth()->id();
            $log->save();

            $forml = FormL::doesntHave('formu')->with(['tenure.entity','status']);

            if(auth()->user()->hasRole('ks')) {
                $forml = $forml->whereHas('tenure', function($tenure) {
                    return $tenure->where('entity_type', auth()->user()->entity_type)->where('entity_id', auth()->user()->entity_id);
                });
            }
            else if(auth()->user()->hasAnyRole(['ptw','pthq'])) {
                $forml = $forml->where('filing_status_id', '>', 1)->where(function($forml) {
                    return $forml->whereHas('distributions', function($distributions) {
                        return $distributions->where('assigned_to_user_id', auth()->id());
                    })->orWhere(function($forml){
                        if(auth()->user()->hasRole('ptw'))
                            return $forml->whereDoesntHave('logs', function($logs) {
                                return $logs->where('activity_type_id', 12)->where('filing_status_id','<=', 3);
                            });
                        else
                            return $forml->whereHas('logs', function($logs) {
                                return $logs->where('role_id', 8)->where('activity_type_id', 14);
                            });
                    });
                });
            }
            else {
                $forml = $forml->where('filing_status_id', '>', 1)->whereHas('distributions', function($distributions) {
                    return $distributions->where('assigned_to_user_id', auth()->id());
                });
            }

            return datatables()->of($forml)
                ->editColumn('tenure.entity.name', function ($forml) {
                    return $forml->tenure->entity->name;
                })
                ->editColumn('entity_type', function ($forml) {
                    return $forml->tenure->entity_type == "App\\UserUnion" ? 'Kesatuan' : 'Persekutuan';
                })
                ->editColumn('applied_at', function ($forml) {
                    return $forml->applied_at ? date('d/m/Y', strtotime($forml->applied_at)) : '-';
                })
                ->editColumn('status.name', function ($forml) {
                    if($forml->filing_status_id == 9)
                        return '<span class="badge badge-success">'.$forml->status->name.'</span>';
                    else if($forml->filing_status_id == 8)
                        return '<span class="badge badge-danger">'.$forml->status->name.'</span>';
                    else if($forml->filing_status_id == 7)
                        return '<span class="badge badge-warning">'.$forml->status->name.'</span>';
                    else
                        return '<span class="badge badge-default">'.$forml->status->name.'</span>';
                })
                ->editColumn('letter', function($forml) {
                    $result = "";
                    if($forml->filing_status_id == 9){
                        $result .= letterButton(35, get_class($forml), $forml->id);
                    }
                    return $result;
                })
                ->editColumn('action', function ($forml) {
                    $button = "";
                    $button .= '<a onclick="viewFiling(\''.addslashes(get_class($forml)).'\','.$forml->id.')" href="javascript:;" class="btn btn-default btn-xs mb-1"><i class="fa fa-search mr-1"></i> Lihat</a><br>';

                    if((auth()->user()->hasAnyRole(['ppw','pphq']) || (auth()->user()->hasRole('ks') && $forml->is_editable)) && $forml->filing_status_id < 7 )
                        $button .= '<a href="'.route('forml.item', $forml->id).'" class="btn btn-primary btn-xs mb-1"><i class="fa fa-edit mr-1"></i> Kemaskini Borang</a><br>';

                    if( ((auth()->user()->hasRole('ptw') && $forml->distributions->count() == 0) || (auth()->user()->hasRole('pthq') && $forml->distributions->count() == 3)) && $forml->filing_status_id < 7 )
                        $button .= '<a onclick="receive('.$forml->id.')" href="javascript:;" class="btn btn-info btn-xs mb-1"><i class="fa fa-check mr-1"></i> Terima Dokumen Fizikal</a><br>';

                    if(auth()->user()->hasAnyRole(['ppw','pw', 'pphq', 'pkpg', 'kpks']) && $forml->filing_status_id < 8 )
                        $button .= '<a onclick="query('.$forml->id.')" href="javascript:;" class="btn btn-warning btn-xs mb-1"><i class="fa fa-question mr-1"></i> Kuiri</a><br>';

                    if(auth()->user()->hasAnyRole(['ppw','pw', 'pphq', 'pkpg']) && $forml->filing_status_id < 7 )
                        $button .= '<a onclick="recommend('.$forml->id.')" href="javascript:;" class="btn btn-warning btn-xs mb-1"><i class="fa fa-comment mr-1"></i> Ulasan/Syor</a><br>';

                    if(auth()->user()->hasRole('kpks') && $forml->filing_status_id < 8 )
                        $button .= '<a onclick="process('.$forml->id.')" href="javascript:;" class="btn btn-success btn-xs mb-1"><i class="fa fa-spinner mr-1"></i> Proses</a><br>';

                    return $button;
                })
                ->make(true);
        }
        else {
            $log = new LogSystem;
            $log->module_id = 20;
            $log->activity_type_id = 9;
            $log->description = "Buka paparan Borang L";
            $log->url = $request->fullUrl();
            $log->method = strtoupper($request->method());
            $log->ip_address = $request->ip();
            $log->created_by_user_id = auth()->id();
            $log->save();
        }

        return view('change-officer.l.list');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request) {

        $tenures = auth()->user()->entity->tenures()->whereDoesntHave('forml', function($forml) {
            return $forml->doesntHave('formu');
        })->get();
        $branches = Branch::where('user_union_id', auth()->user()->entity->id)->get();

        $forml = FormL::create([
            'tenure_id' => auth()->user()->entity->tenures->last()->id,
            'address_id' => auth()->user()->entity->addresses->last()->address->id,
            'created_by_user_id' => auth()->id(),
        ]);

        $error_list = $this->getErrors($forml);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 4;
        $log->description = "Tambah Borang L";
        $log->data_new = json_encode($forml);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        return view('change-officer.l.index', compact('tenures','branches','forml','error_list'));
     }

     /**
      * Show the application dashboard.
      *
      * @return \Illuminate\Http\Response
      */
     public function edit(Request $request) {

        $tenures = auth()->user()->entity->tenures;
        $branches = Branch::where('user_union_id', auth()->user()->entity->id);

        $forml = FormL::findOrFail($request->id);

        $error_list = $this->getErrors($forml);

        return view('change-officer.l.index', compact('tenures','branches','forml','error_list'));
     }

     private function getErrors($forml) {

        $errors = [];

        if(!$forml) {
            $errors['l'] = [null,null];
        }
        else {
            $errors_l = [];

            $validate_forml = Validator::make($forml->toArray(), [
                'resolved_at' => 'required',
                'meeting_type_id' => 'required|integer',
            ]);

            if ($validate_forml->fails())
                $errors_l = array_merge($errors_l, $validate_forml->errors()->toArray());

            // if($forml->tenure->officers->count() < 5)
            //     $errors_l = array_merge($errors_l, ['officers' => ['Jumlah pegawai yang meninggalkan jawatan kurang dari 5 orang.']]);

            // if($forml->tenure->officers->count() < 5)
            //     $errors_l = array_merge($errors_l, ['officers' => ['Jumlah pegawai yang dilantik kurang dari 5 orang.']]);

            $errors['l'] = $errors_l;
        }

        return $errors;
    }

    public function praecipe(Request $request) {
        $forml = FormL::findOrFail($request->id);

        $pdf = PDF::loadView('change-officer.l.praecipe', compact('forml'));
        return $pdf->setPaper('A4')->setOrientation('portrait')->download('praecipe.pdf');
    }

    /**
     * Show the form for Form L
     *
     * @return \Illuminate\Http\Response
     */
    public function forml(Request $request) {

        $branches = Branch::where('user_union_id', auth()->user()->entity->id)->get();
        $meeting_type = MasterMeetingType::whereIn('id', [2,3,5])->get();
        $states = MasterState::all();
        $countries = MasterCountry::all();
        $designations = MasterDesignation::all();

        $forml = FormL::findOrFail($request->id);

        $tenure = auth()->user()->entity->tenures->last();
        $previous = auth()->user()->entity->tenures()->where('id', '<', $tenure->id)->orderBy('start_year', 'desc')->first();
        $officers = $previous->officers;

        return view('change-officer.l.forml.index', compact('branches','forml','states','officers','meeting_type', 'designations', 'countries'));
    }

    /**
     * Validate the application
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request) {

        $forml = FormL::findOrFail($request->id);
        $error_list = $this->getErrors($forml);
        $errors = $error_list['l'];

        //return response()->json(['errors' => $errors], 422);

        if(count($errors) > 0)
            return response()->json(['status' => 'error', 'title' => 'Harap Maaf!', 'message' => 'Anda masih belum melengkapkan borang ini. Sila semak semula.']);

        else {
            $log = new LogSystem;
            $log->module_id = 20;
            $log->activity_type_id = 5;
            $log->description = "Kemaskini Borang L - Hantar Notis";
            $log->url = $request->fullUrl();
            $log->method = strtoupper($request->method());
            $log->ip_address = $request->ip();
            $log->created_by_user_id = auth()->id();
            $log->save();

            $forml = FormL::findOrFail($request->id);

            $forml->logs()->updateOrCreate(
                [
                    'module_id' => 20,
                    'activity_type_id' => 11,
                    'filing_status_id' => $forml->filing_status_id,
                    'created_by_user_id' => auth()->id(),
                    'role_id' => auth()->user()->roles->last()->id,
                ],
                [
                    'data' => ''
                ]
            );

            $forml->filing_status_id = 2;
            $forml->is_editable = 0;
            $forml->applied_at = date('Y-m-d');
            $forml->save();

            $forml->references()->updateOrCreate(
                [ 'reference_type_id' => 1 ],[
                'reference_no' => '-',
                'module_id' => 20,
            ]);

            Mail::to($forml->created_by->email)->send(new Sent($forml, 'Penghantaran Notis Perubahan Pegawai'));

            return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Notis anda telah dihantar. Sila hantar dokumen fizikal dalam masa 7 hari.']);
        }
    }

    // Leaving Officer CRUD START
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function leaving_index(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 1;
        $log->description = "Papar senarai Pegawai Borang L - Butiran Pegawai Yang Meninggalkan Jawatan";
        $log->data_old = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $officers = FormLLeaving::where('forml_id', $request->id)->with(['officer.designation']);

        return datatables()->of($officers)
            ->editColumn('left_at', function ($officer) {
                return $officer->left_at ? date('d/m/Y', strtotime($officer->left_at)) : '';
            })
            ->editColumn('action', function ($officer) {
                $button = "";

                $button .= '<a onclick="editLeaving('.$officer->officer_id.')" href="javascript:;" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a> ';
                $button .= '<a onclick="removeLeaving('.$officer->officer_id.')" href="javascript:;" class="btn btn-danger btn-xs "><i class="fa fa-trash"></i></a> ';
                return $button;
            })
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function leaving_insert(Request $request) {
        $validator = Validator::make($request->all(), [
            'leaving_id' => 'unique:forml_leaving,officer_id,null,null,forml_id,'.$request->id,
            'left_at' => 'required',
        ]);

        if ($validator->fails()) {
            // If validation fails
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $forml = FormL::findOrFail($request->id);

        $officer = FormLLeaving::create([
            'forml_id' => $forml->id,
            'officer_id' => $request->officer_id,
            'left_at' => Carbon::createFromFormat('d/m/Y', $request->left_at)->toDateString(),
        ]);

        $count = $forml->leaving->count();

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 4;
        $log->description = "Tambah Borang L - Butiran Pegawai Yang Meninggalkan Jawatan";
        $log->data_new = json_encode($officer);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data baru telah ditambah.', 'count' => $count]);
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function leaving_edit(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup kemaskini Borang L - Butiran Pegawai Yang Meninggalkan Jawatan";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $leaving_officer = FormLLeaving::where('officer_id', $request->leaving_id)->firstOrFail();
        $forml = FormL::findOrFail($request->id);
        $officers = $forml->tenure->officers;

        return view('change-officer.l.forml.tab2.edit', compact('leaving_officer', 'forml','officers'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function leaving_update(Request $request) {

        $validator = Validator::make($request->all(), [
            'left_at' => 'required',
        ]);

        if ($validator->fails()) {
            // If validation fails
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $officer = FormLLeaving::where('forml_id', $request->id)
                               ->where('officer_id', $request->leaving_id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini Borang L - Butiran Pegawai Yang Meninggalkan Jawatan";
        $log->data_old = json_encode($officer);

        $officer->update([
            'officer_id' => $request->officer_id,
            'left_at' => Carbon::createFromFormat('d/m/Y', $request->left_at)->toDateString(),
        ]);

        $log->data_new = json_encode($officer);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dikemaskini.']);
    }

    /**
     * Remove the specified resource from storage.
     * @param  Request $request
     * @return Response
     */
    public function leaving_delete(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $officer = FormLLeaving::where('forml_id', $request->id)
                               ->where('officer_id', $request->leaving_id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 6;
        $log->description = "Padam Borang L - Butiran Pegawai Yang Meninggalkan Jawatan";
        $log->data_old = json_encode($officer);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $officer->delete();

        $count = $forml->leaving->count();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dipadam.', 'count' => $count]);
    }
    // Leaving Officer CRUD END

    // Holding Officer CRUD START
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function officer_index(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 1;
        $log->description = "Papar senarai Pegawai Borang L - Butiran Pegawai Yang Memegang Jawatan";
        $log->data_old = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $officers = FormLOfficer::where('forml_id', $request->id);

        return datatables()->of($officers)
            ->editColumn('designation.name', function($officer) {
                return $officer->designation->name;
            })
            ->editColumn('held_at', function ($officer) {
                return $officer->held_at ? date('d/m/Y', strtotime($officer->held_at)) : '';
            })
            ->editColumn('action', function ($officer) {
                $button = "";

                $button .= '<a onclick="editOfficer('.$officer->id.')" href="javascript:;" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a> ';
                $button .= '<a onclick="removeOfficer('.$officer->id.')" href="javascript:;" class="btn btn-danger btn-xs "><i class="fa fa-trash"></i></a> ';

                return $button;
            })
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function officer_insert(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'identification_no' => 'required|string',
            'occupation' => 'required|string',
            'birth_date' => 'required',
        ]);

        if ($validator->fails()) {
            // If validation fails
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $forml = FormL::findOrFail($request->id);

        $address = Address::create($request->all());
        $request->request->add(['address_id' => $address->id]);
        $request->request->add(['forml_id' => $forml->id]);
        $request->request->add(['date_of_birth' => Carbon::createFromFormat('d/m/Y', $request->birth_date)->toDateTimeString()]);
        $request->request->add(['held_at' => Carbon::createFromFormat('d/m/Y', $request->start_date)->toDateTimeString()]);

        $officer = FormLOfficer::create($request->all());

        $count = $forml->officers->count();

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 4;
        $log->description = "Tambah Borang L - Butiran Pegawai Yang Memegang Jawatan";
        $log->data_new = json_encode($officer);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data baru telah ditambah.', 'count' => $count]);
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function officer_edit(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup kemaskini Borang L - Butiran Pegawai Yang Memegang Jawatan";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $states = MasterState::all();
        $countries = MasterCountry::all();
        $designations = MasterDesignation::all();
        $officer = FormLOfficer::findOrFail($request->officer_id);
        $forml = FormL::findOrFail($request->id);

        return view('change-officer.l.forml.tab3.edit', compact('officer', 'forml','states', 'countries', 'designations'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function officer_update(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'identification_no' => 'required|string',
            'occupation' => 'required|string',
            'birth_date' => 'required',
        ]);

        if ($validator->fails()) {
            // If validation fails
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $officer = FormLOfficer::findOrFail($request->officer_id);

        $address = Address::findOrFail($officer->address_id)->update($request->all());
        $request->request->add(['forml_id' => $request->id]);
        $request->request->add(['date_of_birth' => Carbon::createFromFormat('d/m/Y', $request->birth_date)->toDateTimeString()]);
        $request->request->add(['held_at' => Carbon::createFromFormat('d/m/Y', $request->start_date)->toDateTimeString()]);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini Borang L - Butiran Pegawai Yang Memegang Jawatan";
        $log->data_old = json_encode($officer);

        $officer->update($request->all());

        $log->data_new = json_encode($officer);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dikemaskini.']);
    }

    /**
     * Remove the specified resource from storage.
     * @param  Request $request
     * @return Response
     */
    public function officer_delete(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $officer = FormLOfficer::findOrFail($request->officer_id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 6;
        $log->description = "Padam Borang L - Butiran Pegawai Yang Memegang Jawatan";
        $log->data_old = json_encode($officer);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $officer->delete();

        $count = $forml->officers->count();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dipadam.', 'count' => $count]);
    }
    // Holding Officer CRUD END

    /**
     * Distribute the application to specific user
     *
     * @return \Illuminate\Http\Response
     */
    private function distribute($forml, $target) {

        $check = $forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) use($target) {
            return $role->where('name', trim(strtolower($target)));
        });

        if($check->count() > 0)
            return;

        if($target == "ptw") {
            if($forml->distributions()->where('filing_status_id', 2)->count() > 1)
                return;

            // Distribute based on portfolio
            $ptw = ViewUserDistributionPTW::where('filing_type', 'App\FilingModel\FormL')->where('filing_status_id', 2)->orderBy('count');

            if($ptw->count() > 0)
                $ptw = $ptw->first();
            else
                $ptw = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('province_office_id', auth()->user()->entity->province_office_id)->where('role_id', 6)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => auth()->id()
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );
        }
        else if($target == "ppw") {
            if($forml->distributions()->where('filing_status_id', 3)->count() > 2)
                return;

            // Distribute based on portfolio
            $ppw = ViewUserDistributionPPW::where('province_office_id', auth()->user()->entity->province_office_id)->where('filing_type', 'App\FilingModel\FormL')->where('filing_status_id', 3)->orderBy('count');

            if($ppw->count() > 0)
                $ppw = $ppw->first();
            else
                $ppw = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('province_office_id', auth()->user()->entity->province_office_id)->where('role_id', 7)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => $ppw->user->id
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );

            Mail::to($ppw->user->email)->send(new Distributed($forml, 'Serahan Notis Perubahan Pegawai'));
        }
        else if($target == "pw") {
            if($forml->distributions()->where('filing_status_id', 6)->count() > 1)
                return;

            // Distribute based on portfolio
            $pw = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('province_office_id', auth()->user()->entity->province_office_id)->where('role_id', 8)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => $pw->user->id
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );

            Mail::to($pw->user->email)->send(new Distributed($forml, 'Serahan Notis Perubahan Pegawai'));
        }
        else if($target == "pthq") {
            if($forml->distributions()->where('filing_status_id', 6)->count() > 2)
                return;

            // Distribute based on portfolio
            $pthq = ViewUserDistributionPTHQ::where('filing_type', 'App\FilingModel\FormL')->where('filing_status_id', 6)->orderBy('count');

            if($pthq->count() > 0)
                $pthq = $pthq->first();
            else
                $pthq = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('role_id',9)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => auth()->id()
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );
        }
        else if($target == "pphq") {
            if($forml->distributions()->where('filing_status_id', 4)->count() > 2)
                return;

            // Distribute based on portfolio
            $pphq = ViewUserDistributionPPHQ::where('province_office_id', auth()->user()->entity->province_office_id)->where('filing_type', 'App\FilingModel\FormL')->where('filing_status_id', 3)->orderBy('count');

            if($pphq->count() > 0)
                $pphq = $pphq->first();
            else
                $pphq = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('role_id',10)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => $pphq->user->id
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );

            Mail::to($pphq->user->email)->send(new Distributed($forml, 'Serahan Notis Perubahan Pegawai'));
        }
        else if($target == "pkpg") {
            if($forml->distributions()->where('filing_status_id', 6)->count() > 3)
                return;

            // Distribute based on portfolio
            $pkpg = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('role_id',12)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => $pkpg->user->id
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );

            Mail::to($pkpg->user->email)->send(new Distributed($forml, 'Serahan Notis Perubahan Pegawai'));
        }
        else if($target == "kpks") {
            if($forml->distributions()->where('filing_status_id', 6)->count() > 4)
                return;

            // Distribute based on portfolio
            $kpks = UserStaff::whereHas('user', function($user) { return $user->where('user_status_id', 1); })->where('role_id',17)->first();

            $forml->distributions()->updateOrCreate(
                [
                    'filing_status_id' => $forml->filing_status_id,
                    'assigned_to_user_id' => $kpks->user->id
                ],
                [
                    'updated_at' => Carbon::now()
                ]
            );

            Mail::to($kpks->user->email)->send(new Distributed($forml, 'Serahan Notis Perubahan Pegawai'));
        }
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function process_documentReceive_edit(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup (Proses) Borang L - Terima Dokumen Fizikal";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $route = route('forml.process.document-receive', $forml->id);

        return view('general.modal.document-receive', compact('route'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_documentReceive_update(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini (Proses) Borang L - Terima Dokumen Fizikal";
        $log->data_new = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $forml = FormL::findOrFail($request->id);

        $forml->filing_status_id = 3;
        $forml->save();

        $forml->references()->updateOrCreate(
            [
                'reference_type_id' => auth()->user()->hasAnyRole(['ptw','ppw','pw']) ? 1 : 2,
            ],
            [
                'reference_no' => $request->reference_no,
                'module_id' => 20,
            ]
        );

        $forml->logs()->updateOrCreate(
            [
                'module_id' => 20,
                'activity_type_id' => 12,
                'filing_status_id' => $forml->filing_status_id,
                'created_by_user_id' => auth()->id(),
                'role_id' => auth()->user()->entity->role_id
            ],
            [
                'data' => $request->received_at
            ]
        );

        $this->distribute($forml, auth()->user()->entity->role->name);

        if(auth()->user()->hasRole('ptw')) {
            $this->distribute($forml, 'ppw');
            Mail::to($forml->created_by->email)->send(new Received(auth()->user(), $forml, 'Pengesahan Penerimaan Notis Perubahan Pegawai'));
        }
        else if(auth()->user()->hasRole('pthq')) {
            $this->distribute($forml, 'pphq');
            Mail::to($forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) { return $role->where('name', 'ppw'); })->first()->assigned_to->email)->send(new ReceivedHQ(auth()->user(), $forml, 'Pengesahan Penerimaan Notis Perubahan Pegawai'));
            Mail::to($forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) { return $role->where('name', 'pw'); })->first()->assigned_to->email)->send(new ReceivedHQ(auth()->user(), $forml, 'Pengesahan Penerimaan Notis Perubahan Pegawai'));
        }

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dikemaskini.']);
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function process_query_edit(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup (Proses) Borang L - Kuiri";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $route = route('forml.process.query.item', $forml->id);
        $route2 = route('forml.process.query', $forml->id);

        return view('general.modal.query', compact('route','route2'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_query_update(Request $request) {

        $forml = FormL::findOrFail($request->id);

        if(count($forml->queries()->whereNull('log_filing_id')->get()) == 0) {
            return response()->json(['status' => 'error', 'title' => 'Harap Maaf!', 'message' => 'Sila masukkan sekurang-kurangnya satu (1) kuiri sebelum hantar.']);
        }

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini (Proses) Borang L - Kuiri";
        $log->data_new = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $forml->filing_status_id = 5;
        $forml->is_editable = 1;
        $forml->save();

        $log2 = $forml->logs()->updateOrCreate(
            [
                'module_id' => 20,
                'activity_type_id' => 13,
                'filing_status_id' => $forml->filing_status_id,
                'created_by_user_id' => auth()->id(),
                'role_id' => auth()->user()->entity->role_id
            ],
            [
                'data' => ''
            ]
        );

        if(auth()->user()->hasRole('pw')) {
            // Send to PPW
            $log = $forml->logs()->where('role_id', 7)->get()->last();
            Mail::to($log->created_by->email)->send(new Queried(auth()->user(), $forml, 'Kuiri Permohonan Borang L oleh PW'));
        } else if(auth()->user()->hasRole('pkpg')) {
            // Send to PPHQ
            $log = $forml->logs()->where('role_id', 10)->get()->last();
            Mail::to($log->created_by->email)->send(new Queried(auth()->user(), $forml, 'Kuiri Permohonan Borang L oleh PKPG'));
        } else if(auth()->user()->hasRole('kpks')) {
            // Send to PKPG
            $log = $forml->logs()->where('role_id', 12)->get()->last();
            Mail::to($log->created_by->email)->send(new Queried(auth()->user(), $forml, 'Kuiri Permohonan Borang L oleh KPKS'));
        }
        else {
            // Send to KS
            Mail::to($forml->created_by->email)->send(new Queried(auth()->user(), $forml, 'Kuiri Permohonan Borang L'));
        }

        $forml->queries()->where('created_by_user_id', auth()->id())->whereNull('log_filing_id')->update(['log_filing_id' => $log2->id]);

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dikemaskini.']);
    }

    /**
     * Show the list of instance
     *
     * @return \Illuminate\Http\Response
     */
    public function process_query_item_list(Request $request) {
        if($request->ajax()) {
            $log = new LogSystem;
            $log->module_id = 20;
            $log->activity_type_id = 1;
            $log->description = "Papar senarai (Proses) Borang L - Kuiri";
            $log->data_old = json_encode($request->input());
            $log->url = $request->fullUrl();
            $log->method = strtoupper($request->method());
            $log->ip_address = $request->ip();
            $log->created_by_user_id = auth()->id();
            $log->save();

            $forml = FormL::findOrFail($request->id);

            $queries = $forml->queries()->where('created_by_user_id', auth()->id())->whereNull('log_filing_id');

            return datatables()->of($queries)
                ->editColumn('action', function ($query) {
                    $button = "";
                    $button .= '<a href="javascript:;" onclick="edit(this,'.$query->id.')" class="btn btn-success btn-xs" data-toggle="tooltip" title="Kemaskini"><i class="fa fa-edit"></i></a> ';
                    $button .= '<a href="javascript:;" onclick="remove('.$query->id.')" class="btn btn-danger btn-xs" data-toggle="tooltip" title="Padam"><i class="fa fa-trash"></i></a>';
                    return $button;
                })
                ->make(true);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_query_item_update(Request $request) {

        $forml = FormL::findOrFail($request->id);

        if($request->query_id) {
            $query = Query::findOrFail($request->query_id);

            $log = new LogSystem;
            $log->module_id = 20;
            $log->activity_type_id = 5;
            $log->description = "Kemaskini (Proses) Borang L - Kuiri";
            $log->data_old = json_encode($query);

            $query->update([
                'content' => $request->content
            ]);

            $log->data_new = json_encode($query);
            $log->url = $request->fullUrl();
            $log->method = strtoupper($request->method());
            $log->ip_address = $request->ip();
            $log->created_by_user_id = auth()->id();
            $log->save();

            return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dikemaskini.']);
        }
        else {
            $query = $forml->queries()->create([
                'content' => $request->content,
                'created_by_user_id' => auth()->id(),
            ]);

            $log = new LogSystem;
            $log->module_id = 20;
            $log->activity_type_id = 4;
            $log->description = "Tambah (Proses) Borang L - Kuiri";
            $log->data_new = json_encode($query);
            $log->url = $request->fullUrl();
            $log->method = strtoupper($request->method());
            $log->ip_address = $request->ip();
            $log->created_by_user_id = auth()->id();
            $log->save();

            return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah disimpan.']);
        }


    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_query_item_delete(Request $request) {
        $query = Query::findOrFail($request->query_id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 6;
        $log->description = "Padam (Proses) Borang L - Kuiri";
        $log->data_old = json_encode($query);
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $query->delete();

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dipadam.']);
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function process_recommend_edit(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup (Proses) Borang L - Ulasan / Syor";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $recommendation = $forml->logs()->where('activity_type_id',14)->where('filing_status_id', $forml->filing_status_id)->where('created_by_user_id', auth()->id());

        if($recommendation->count() > 0)
            $recommendation = $recommendation->first();
        else
            $recommendation = new LogFiling;

        $route = route('forml.process.recommend', $forml->id);

        return view('general.modal.recommend', compact('route', 'recommendation'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_recommend_update(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini (Proses) Borang L - Ulasan / Syor";
        $log->data_new = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $forml = FormL::findOrFail($request->id);
        $forml->filing_status_id = 6;
        $forml->is_editable = 0;
        $forml->save();

        $forml->logs()->updateOrCreate(
            [
                'module_id' => 20,
                'activity_type_id' => 14,
                'filing_status_id' => $forml->filing_status_id,
                'created_by_user_id' => auth()->id(),
                'role_id' => auth()->user()->entity->role_id
            ],
            [
                'data' => $request->data
            ]
        );

        if(auth()->user()->hasRole('ppw'))
            $this->distribute($forml, 'pw');
        else if(auth()->user()->hasRole('pphq'))
            $this->distribute($forml, 'pkpg');
        else if(auth()->user()->hasRole('pkpg'))
            $this->distribute($forml, 'kpks');
        else if(auth()->user()->hasRole('pw'))
            Mail::to($forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) { return $role->where('name', 'ptw'); })->first()->assigned_to->email)->send(new SendToHQ(auth()->user(), $forml, 'Serahan Notis Perubahan Pegawai ke Ibu Pejabat'));

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Data telah dikemaskini.']);
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function process_result(Request $request) {

        $form = $forml = FormL::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup (Proses) Borang L - Keputusan";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $route_reject = route("forml.process.result.reject", $form->id);
        $route_approve = route("forml.process.result.approve", $form->id);

        return view('general.modal.result-officer', compact('route_reject','route_approve'));
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function process_result_approve_edit(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup (Proses) Borang L - Lulus";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $route = route('forml.process.result.approve', $forml->id);

        return view('general.modal.register', compact('route'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_result_approve_update(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini (Proses) Borang L - Lulus";
        $log->data_new = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $forml = FormL::findOrFail($request->id);
        $forml->filing_status_id = 9;
        $forml->is_editable = 0;
        $forml->save();

        $forml->logs()->updateOrCreate(
            [
                'module_id' => 20,
                'activity_type_id' => 16,
                'filing_status_id' => $forml->filing_status_id,
                'created_by_user_id' => auth()->id(),
                'role_id' => auth()->user()->entity->role_id
            ],
            [
                'data' => $request->data
            ]
        );

        $leaving = $forml->leaving()->select('officer_id as id')->get()->toArray();
        $tenure = $forml->tenure->entity->tenures->last();
        $previous = $forml->tenure->entity->tenures()->where('id', '<', $tenure->id)->orderBy('start_year', 'desc')->first();
        $remainders = $previous->officers()->whereNotIn('id', $leaving)->get();

        foreach($forml->officers as $new) {
            $officer = $tenure->officers()->create($new->toArray());
            $officer->age = Carbon::createFromFormat('Y-m-d', $new->date_of_birth)->diffInYears(Carbon::now());
            $officer->save();
        }

        foreach($remainders as $remainder) {
            if(!$forml->officers()->where('identification_no', $remainder->identification_no)->first())
                $tenure->officers()->create($remainder->toArray());
        }

        Mail::to($forml->created_by->email)->send(new ApprovedKS($forml, 'Status Notis Perubahan Pegawai'));
        Mail::to($forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) { return $role->where('name', 'ptw'); })->first()->assigned_to->email)->send(new ApprovedPWN($forml, 'Status Notis Perubahan Pegawai'));
        Mail::to($forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) { return $role->where('name', 'ppw'); })->first()->assigned_to->email)->send(new ApprovedPWN($forml, 'Status Notis Perubahan Pegawai'));
        Mail::to($forml->distributions()->whereHas('assigned_to.entity_staff.role', function($role) { return $role->where('name', 'pw'); })->first()->assigned_to->email)->send(new ApprovedPWN($forml, 'Status Notis Perubahan Pegawai'));

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Permohonan Kesatuan Sekerja ini telah diluluskan. Pegawai Tadbir HQ akan dimaklumkan melalui emel.']);
    }

    /**
     * Show the specified resource.
     * @param  Request $request
     * @return Response
     */
    public function process_result_reject_edit(Request $request) {

        $forml = FormL::findOrFail($request->id);

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 3;
        $log->description = "Popup (Proses) Borang L - Tidak Lulus";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $route = route('forml.process.result.reject', $forml->id);

        return view('general.modal.not-register', compact('route'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function process_result_reject_update(Request $request) {

        $log = new LogSystem;
        $log->module_id = 20;
        $log->activity_type_id = 5;
        $log->description = "Kemaskini (Proses) Borang L - Tidak Lulus";
        $log->data_new = json_encode($request->input());
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        $forml = FormL::findOrFail($request->id);
        $forml->filing_status_id = 8;
        $forml->is_editable = 0;
        $forml->save();

        $forml->logs()->updateOrCreate(
            [
                'module_id' => 20,
                'activity_type_id' => 16,
                'filing_status_id' => $forml->filing_status_id,
                'created_by_user_id' => auth()->id(),
                'role_id' => auth()->user()->entity->role_id
            ],
            [
                'data' => $request->data
            ]
        );

        Mail::to($forml->created_by->email)->send(new Rejected($forml, 'Status Notis Perubahan Pegawai'));

        return response()->json(['status' => 'success', 'title' => 'Berjaya!', 'message' => 'Permohonan Kesatuan Sekerja ini telah ditolak. Kesatuan Sekerja akan dimaklumkan melalui emel.']);
    }

    public function download(Request $request) {

        $filing = FormL::findOrFail($request->id);                                                      // Change here
        setlocale(LC_TIME, "ms", "my_MS", "ms_MY");
        $data = [                                                                                       // Change here
            'entity_name' => htmlspecialchars($filing->tenure->entity->name),
            'entity_address' => htmlspecialchars($filing->address->address1).
                ($filing->address->address2 ? ', '.htmlspecialchars($filing->address->address2) : '').
                ($filing->address->address3 ? ', '.htmlspecialchars($filing->address->address3) : '').
                ', '.($filing->address->postcode).
                ($filing->address->district ? ' '.htmlspecialchars($filing->address->district->name) : '').
                ($filing->address->state ? ', '.htmlspecialchars($filing->address->state->name) : ''),
            'registration_no' => htmlspecialchars($filing->tenure->entity->registration_no),
            'branch_name' => $filing->branch ? htmlspecialchars($filing->branch->name) : '',
            'branch_address' => $filing->branch ?
                (', '.htmlspecialchars($filing->branch->address->address1).
                ($filing->branch->address->address2 ? ', '.htmlspecialchars($filing->branch->address->address2) : '').
                ($filing->branch->address->address3 ? ', '.htmlspecialchars($filing->branch->address->address3) : '').
                ', '.($filing->branch->address->postcode).
                ($filing->branch->address->district ? ' '.htmlspecialchars($filing->branch->address->district->name) : '').
                ($filing->branch->address->state ? ', '.htmlspecialchars($filing->branch->address->state->name) : '')) : '',
            'is_branch' => $filing->branch ? htmlspecialchars('Cawangan') : htmlspecialchars('Kesatuan'),
            'consultant_address' => htmlspecialchars($filing->consultant_address),
            'meeting_type' => $filing->meeting_type ? htmlspecialchars($filing->meeting_type->name) : '',
            'resolved_day' => htmlspecialchars(strftime('%e', strtotime($filing->resolved_at))),
            'resolved_month_year' => htmlspecialchars(strftime('%B %Y', strtotime($filing->resolved_at))),
            'today_day' => htmlspecialchars(strftime('%e')),
            'today_month_year' =>  htmlspecialchars(strftime('%B %Y')),
        ];

        $log = new LogSystem;
        $log->module_id = 20;                                                                            // Change here
        $log->activity_type_id = 19;
        $log->description = "Cetak Dokumen";
        $log->url = $request->fullUrl();
        $log->method = strtoupper($request->method());
        $log->ip_address = $request->ip();
        $log->created_by_user_id = auth()->id();
        $log->save();

        // Creating the new document...
        $phpWord = new PhpWord();

        //Searching for values to replace
        $document = $phpWord->loadTemplate(storage_path('templates/filings/change-officer/forml.docx'));        // Change here

        foreach($data as $key => $value) {
            $document->setValue($key, $value);
        }

        // Generate table leaving
        $rows = $filing->leaving;
        $document->cloneRow('leave_name', count($rows));

        foreach($rows as $index => $row) {
            $document->setValue('leave_name#'.($index+1), htmlspecialchars($row->officer->name));
            $document->setValue('leave_designation#'.($index+1), ($row->officer->designation ? htmlspecialchars(strtoupper($row->officer->designation->name)) : ''));
            $document->setValue('leave_at#'.($index+1), htmlspecialchars(date('d/m/Y', strtotime($row->left_at))));
        }

        // Generate table officer
        $rows2 = $filing->officers;
        $document->cloneRow('officer_designation', count($rows2));

        foreach($rows2 as $index => $row2) {
            $document->setValue('officer_designation#'.($index+1), ($row2->designation ? htmlspecialchars(strtoupper($row2->designation->name)) : ''));
            $document->setValue('officer_name#'.($index+1), htmlspecialchars(strtoupper($row2->name)));
            $document->setValue('officer_ic#'.($index+1), htmlspecialchars($row2->identification_no));
            $document->setValue('officer_dob#'.($index+1), htmlspecialchars(date('d/m/Y', strtotime($row2->date_of_birth))));
            $document->setValue('officer_address#'.($index+1), htmlspecialchars(strtoupper($row2->address->address1)).
                ($row2->address->address2 ? ', '.htmlspecialchars(strtoupper($row2->address->address2)) : '').
                ($row2->address->address3 ? ', '.htmlspecialchars(strtoupper($row2->address->address3)) : '').
                ', '.($row2->address->postcode).
                ($row2->address->district ? ' '.htmlspecialchars(strtoupper($row2->address->district->name)) : '').
                ($row2->address->state ? ', '.htmlspecialchars(strtoupper($row2->address->state->name)) : '')
            );
            $document->setValue('officer_occupation#'.($index+1), htmlspecialchars(strtoupper($row2->occupation)));
            $document->setValue('officer_appointed_at#'.($index+1), htmlspecialchars(strtoupper($row2->held_at)));
        }

        // save as a random file in temp file
        $file_name = uniqid().'_'.'Borang L';                                                          // Change here
        $temp_file = storage_path('tmp/'.$file_name.'.docx');
        $document->saveAs($temp_file);

        return docxToPdf($temp_file);
    }
}
