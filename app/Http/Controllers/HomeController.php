<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Client;
use DB;
use Carbon\Carbon;
use Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $month = date('m');
        $year = date('Y');
        if($request->month != null){
            $month = $request->month;
            $invoice_month = date('F', mktime(0, 0, 0, $month, 10));
        }
        if($request->year != null){
            $year = $request->year;
            $invoice_year = $year;
        }

        $month_paid = DB::table('payments')
            ->whereRaw('MONTH(updated_at) = ?',[$month])
            ->whereRaw('YEAR(updated_at) = ?',[$year])
            ->where('status', 2)
            ->where('show_status', 0)
            ->sum('price');
        $last = DB::table('payments')->where('show_status', 0)->where('status', 2)->orderBy('updated_at', 'desc')->first();
        $total_declined = DB::table('payments')
            ->whereRaw('MONTH(updated_at) = ?',[date('m')])
            ->where('status', 1)
            ->where('show_status', 0)
            ->count();
        $total_completed = DB::table('payments')
            ->whereRaw('MONTH(updated_at) = ?',[date('m')])
            ->where('status', 2)
            ->where('show_status', 0)
            ->count();
        $last_payment = Payment::where('show_status', 0)->where('status', 2)->orderBy('id', 'desc')->first();

        $monthly_summary = DB::table('payments')
        ->select( DB::raw('count(payments.status) as count, payments.status'))
        ->groupBy('payments.status')
        ->whereRaw('MONTH(updated_at) = ?',[date('m')])
        ->where('show_status', 0)
        ->get();

        $completed = 0;
        $pending = 0;
        $declined = 0;
        $total = 0;
        foreach($monthly_summary as $key => $value){
            if($value->status == 2){
                $completed = $value->count;
            }
            if($value->status == 0){
                $pending = $value->count;
            }
            if($value->status == 1){
                $declined = $value->count;
            }
            $total = $total + $value->count;
        }

        if($total != 0){
            $completed_percentage = round(($completed / $total) * 100, 2);
            $pending_percentage = round(($pending / $total) * 100, 2);
            $declined_percentage = round(($declined / $total) * 100, 2);
        }else{
            $completed_percentage = 0;
            $pending_percentage = 0;
            $declined_percentage = 0;
        }

        $data = Payment::where('show_status', 0);
        $data = $data->orderBy('id', 'desc')->limit(20)->get();

        $customer = Client::orderBy('id', 'desc')->limit(20)->get();
        $completed_array = [];
        $completed_data = DB::table('payments')->select('price')->where('show_status', 0)->where('status', 2)->get();
        foreach($completed_data as $key => $value){
            array_push($completed_array, $value->price);
        }
        $graph_data = DB::table('payments')
            ->select(DB::raw('SUM(price) as price'), DB::raw('DATE_FORMAT(updated_at,"%a") as invoice_date'))
            ->where('created_at', '>', Carbon::now()->startOfWeek())
            ->where('created_at', '<', Carbon::now()->endOfWeek())
            ->where('status', 2)
            ->where('show_status', 0)
            ->groupBy('updated_at')
            ->orderBy('updated_at', 'asc')
            ->get();
        
        return view('home', compact('data', 'month_paid', 'last', 'total_declined', 'total_completed', 'last_payment', 'completed_percentage', 'pending_percentage', 'declined_percentage', 'customer', 'completed_array', 'graph_data', 'month', 'year'));
    }

    public function showResponse($id){
        $data = Payment::find($id);
        return view('show-response', compact('data'));
    }
    
    public function profile(){
        return view('profile');
    }
    
    public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'confirm_password' => 'required_with:password|same:password',
        ]);
        $input = $request->all();
        if($request->password != null){
            $input['password'] = Hash::make($input['password']);
        }else{
            unset($input['password']);
        }
        auth()->user()->update($input);
        return back()->with('success', 'Profile updated successfully.');
    }
}
