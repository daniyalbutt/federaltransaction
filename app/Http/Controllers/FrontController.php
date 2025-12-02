<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Session;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class FrontController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function payNow($id)
    {
        $data = Payment::where('unique_id', $id)->first();
        if($data->show_status == 1){
            return abort(404);
        }
        if($data->status == 1){
            return redirect()->route('declined.payment', ['id' => $data->id]);
        }
        if($data->status == 2){
            return redirect()->route('success.payment', ['id' => $data->id]);
        }
        $merchant_type = $data->merchants->merchant;
        if($merchant_type == 0){
            return view('stripe', compact('data'));
        }else if($merchant_type == 3){
            return view('payment', compact('data'));
        }else if($merchant_type == 4){
            return view('authorize', compact('data'));
        }else{
            return view('square', compact('data'));
        }
    }
    
    public function paymentSave(Request $request){
        $data = Payment::find($request->input('id'));
        $data->update(['status'=> 2,'return_response'=>'Store Details','payment_data'=>$request->except(['amount','_token','id'])]);
        return redirect()->route('success.payment', ['id' => $data->id]);
    }

    public function invoice($id){
        $data = Payment::where('unique_id', $id)->first();
        return view('invoice', compact('data'));
    }
    
    public function export(){
        $DbName = env('DB_DATABASE');
        $get_all_table_query = "SHOW TABLES ";
        $result = DB::select(DB::raw($get_all_table_query));
    
        $prep = "Tables_in_$DbName";
        foreach ($result as $res){
            $tables[] =  $res->$prep;
        }
    
    
    
        $connect = DB::connection()->getPdo();
    
        $get_all_table_query = "SHOW TABLES";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $result = $statement->fetchAll();
    
    
        $output = '';
        foreach($tables as $table)
        {
            $show_table_query = "SHOW CREATE TABLE " . $table . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();
    
            foreach($show_table_result as $show_table_row)
            {
                $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
            }
            $select_query = "SELECT * FROM " . $table . "";
            $statement = $connect->prepare($select_query);
            $statement->execute();
            $total_row = $statement->rowCount();
    
            for($count=0; $count<$total_row; $count++)
            {
                $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                $table_column_array = array_keys($single_result);
                $table_value_array = array_values($single_result);
                $output .= "\nINSERT INTO $table (";
                $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                $output .= "'" . implode("','", $table_value_array) . "');\n";
            }
        }
        $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
        $file_handle = fopen($file_name, 'w+');
        fwrite($file_handle, $output);
        fclose($file_handle);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_name));
        ob_clean();
        flush();
        readfile($file_name);
        unlink($file_name);
        return Excel::download(new UsersExport, 'users.xlsx');
    }
    
}
