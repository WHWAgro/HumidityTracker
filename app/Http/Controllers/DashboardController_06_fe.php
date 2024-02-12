<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\Fields;
use App\Models\PinHumidity;
use Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if($request->field_name)
        {
            $field_name = $request->field_name;
            $field_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                ->where('fields.field_code',$field_name)
                                ->orWhere('fields.field_name',$field_name)
                                ->get();
            echo json_encode($field_data);
        }
        else
            return view('forecast-data');
    }

    public function forecast_data(Request $request)
    {
        $field_data = [];
        $requested_date = '';
        $requested_field_name = '';
        
        if($request->all())
        {
            $validate = Validator::make($request->all(),[
                'date' => 'required',
                'field_name' => 'required'
            ])->validate();
            $requested_date = $request->date;
            $requested_field_name = $request->field_name;
            $date = $request->date;
            $field_name = $request->field_name;
            $field_data = Fields::join('pins','fields.id','pins.field_id')
                                ->join('pin_humidities','pins.id','pin_humidities.pin_id')
                                ->where('pins.humidity_date','like',$date.'%')
                                ->where('fields.field_code',$field_name)
                                ->orWhere('fields.field_name',$field_name)
                                ->get();
        }
        return view('check-data',compact('field_data','requested_date','requested_field_name'));
    }

    public function field_names(Request $request)
    {
        if(!empty($request->search))
        {
            $field = Fields::select('field_code','field_name')
            ->where('field_code','like','%'.$request->search.'%')
            ->orWhere('field_name','like','%'.$request->search.'%')
            ->get();
            $data_array = [];
            foreach($field as $data)
            {
                if(!in_array($data->field_code,$data_array) || !in_array($data->field_name,$data_array))
                {
                    array_push($data_array,$data->field_code, $data->field_name);
                }
            }
            echo json_encode($data_array);
        }
    }

    public function edit_humidity(Request $request)
    {
        $field_id = $request->field_id;
        $field = PinHumidity::find($field_id);
        $data = '<div class="edit-in">
                    <form action="update_humidity" id="update_humidity" method="POST">
                        <label>
                            Humidity
                            <input class="edit-in_txt humidity" type="text" name="humidity" value="'.$field->humidity.'">
                            <input type="hidden" name="field_id" class="field_id" value="'.$field_id.'">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                            </label>
                        <button class="edit-in-btn save_field">Save</button>
                    </form>
                </div>';
        echo $data;
    }

    public function update_humidity(Request $request)
    {
        $humidity = $request->humidity;
        $field_id = $request->field_id;

        PinHumidity::find($field_id)->update(['humidity'=>$humidity]);
        return response()->json(['success'=>"Humidity Updated"]);
    }

    public function delete_data(Request $request)
    {
        $id = $request->field_id;
        $data = PinHumidity::find($id)->delete();
        return response()->json(['success'=>"Humidity Deleted"]);
    }

    public function exportCSV(Request $request)
    {
        $fileName = 'Humidity'.date('y_m_d').'.csv';
        $tasks = Fields::join('pins','fields.id','pins.field_id')
                        ->join('pin_humidities','pins.id','pin_humidities.pin_id')
                        ->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Date','Field Name', 'Code For Field', 'Alias', 'Razon Social', 'Latitude','Longitude','Humidity');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['humidity_date']  = date("M d,Y",strtotime($task->humidity_date));
                $row['field_name']    = $task->field_name;
                $row['field_code']    = $task->field_code;
                $row['field_alias']  = $task->field_alias;
                $row['razon_social']  = $task->razon_social;
                $row['latitude']  = $task->latitude;
                $row['longitude']  = $task->longitude;
                $row['humidity']  = $task->humidity;

                fputcsv($file, array($row['humidity_date'], $row['field_name'], $row['field_code'], $row['field_alias'], $row['razon_social'],$row['latitude'],$row['longitude'],$row['humidity']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function graph_filter(Request $request)
    {
        $filter_value = $request->filter_value;
        $field_name = $request->field_name;
        $field_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id');
        $x_values = [];
        $y_values = [];
        $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June","July", "Aug", "Sep", "Oct", "Nov", "Dec"];

        if($filter_value == 'current_month')
        {
            $current_month_last = Carbon::now()->endOfMonth()->format('d');
            $last_date = Carbon::now()->format('d');
            $actual_last_date = $last_date+7 > $current_month_last ? $current_month_last : $last_date;
            
            for($i=1;$i<=$actual_last_date;$i++)
            {
                $x_values[] = $i.' '.Carbon::now()->format('M');
            }

            $field_data = $field_data->whereMonth('humidity_date', Carbon::now()->month);

            if($last_date+7 > $current_month_last)
            {
                for($i=1;$i<=7;$i++)
                {
                    $x_values[] = $i.' '.Carbon::now()->addMonth()->format('M');
                }
            
                $field_data = $field_data->whereMonth('humidity_date', Carbon::now()->addMonth()->month);
            }
            
        }

        if($filter_value == 'last_month')
        {
            $last_date = Carbon::now()->subMonth()->endOfMonth()->format('d');

            for($i=1;$i<=$last_date;$i++)
            {
                $x_values[] = $i.' '.Carbon::now()->subMonth()->format('M');
            }

            if(Carbon::now()->subMonth()->month == '12')
            {
                $field_data = $field_data->whereYear('humidity_date',Carbon::now()->subYear()->year)
                                        ->whereMonth('humidity_date', Carbon::now()->subMonth()->month);
            }
            else
            {
                $field_data = $field_data->whereMonth('humidity_date', Carbon::now()->subMonth()->month);
            }
        }

        if($filter_value == 'six_month')
        {
            $start_from = Carbon::now()->subMonth(5)->month;
            $end_to = Carbon::now()->month;
            $count_array = [];
            $sum_array = [];
            $ab = 0;
            if($start_from > $end_to)
            {
                for($i=$start_from;$i<=12;$i++)
                {
                    $monthName = date('F', mktime(0, 0, 0, $i, 10)); 
                    $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereYear('humidity_date',Carbon::now()->subYear()->year)
                                    ->whereMonth('humidity_date',$i) 
                                    ->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                    $count = $f_data->count();
                    $sum = $f_data->sum('humidity');
                    $x_values[] = $monthName.' '.Carbon::now()->subYear()->year;
                    $count_array[$i] = $count;
                    $sum_array[$i] = $sum;
                    $y_values[$ab] = $sum != 0 ? $sum/$count : 0;      
                    $ab++;              
                }

                for($i=$end_to;$i>=1;$i--)
                {
                    $monthName = date('F', mktime(0, 0, 0, $i, 10)); 
                    $x_values[] = $monthName.' '.Carbon::now()->format('Y');
                    $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereYear('humidity_date',Carbon::now()->year)
                                    ->whereMonth('humidity_date',$i) 
                                    ->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                    $count = $f_data->count();
                    $sum = $f_data->sum('humidity');
                    $count_array[$i] = $count;
                    $sum_array[$i] = $sum;
                    $y_values[$ab] = $sum != 0 ? $sum/$count : 0;
                    $ab++;              
                }
                // dd($y_values);
            }
            else
            {
                for($i=Carbon::now()->subMonths(5)->month;$i<=Carbon::now()->month;$i++)
                {
                    $monthName = date('F', mktime(0, 0, 0, $i, 10)); 
                    $x_values[] = $monthName.' '.Carbon::now()->format('Y');
                    $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereYear('humidity_date',Carbon::now()->year)
                                    ->whereMonth('humidity_date',$i) 
                                    ->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                    $count = $f_data->count();
                    $sum = $f_data->sum('humidity');
                    $count_array[$i] = $count;
                    $sum_array[$i] = $sum;
                    $y_values[$ab] = $sum != 0 ? $sum/$count : 0;
                    $ab++;              
                }
            }
            // $field_data = $field_data->where('humidity_date','>=',Carbon::now()->startOfMonth()->subMonths(6)->toDateString())
            //                         ->where('humidity_date','<=',Carbon::now()->format('Y-m-d'));
        }
        
        if($filter_value == 'last_year')
        {
            $start_from = Carbon::now()->subYear()->year;
            $count_array = [];
            $sum_array = [];
            $ab = 0;

            for($i=1;$i<=12;$i++)
            {
                $monthName = date('F', mktime(0, 0, 0, $i, 10)); 
                $x_values[] = $monthName.' '.Carbon::now()->subYear()->year;
                $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereYear('humidity_date',Carbon::now()->subYear()->year)
                                    ->whereMonth('humidity_date',$i) 
                                    ->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                $count = $f_data->count();
                $sum = $f_data->sum('humidity');
                $count_array[$i] = $count;
                $sum_array[$i] = $sum;
                $y_values[$ab] = $sum != 0 ? $sum/$count : 0;
                $ab++;          
            }

            // $field_data = $field_data->whereYear('humidity_date',Carbon::now()->subYear()->year);
        }

        if($filter_value == 'current_year')
        {
            $start_from = Carbon::now()->month;
            $last_date = Carbon::now()->format('d');
            $count_array = [];
            $sum_array = [];
            $ab = 0;
            $actual_last_date = $last_date+7 > 31 ? 31 : $last_date;
            if(Carbon::now()->month == 1)
            {
                for($i=1;$i<=$actual_last_date;$i++)
                {
                    $x_values[] = $i.' '.Carbon::now()->format('M');
                }
            }
            else
            {
                for($i=1;$i<=$start_from;$i++)
                {
                    $monthName = date('F', mktime(0, 0, 0, $i, 10)); 
                    $x_values[] = $monthName.' '.Carbon::now()->year;
                    $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereYear('humidity_date',Carbon::now()->year)
                                    ->whereMonth('humidity_date',$i) 
                                    ->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                    $count = $f_data->count();
                    $sum = $f_data->sum('humidity');
                    $count_array[$i] = $count;
                    $sum_array[$i] = $sum;
                    $y_values[$ab] = $sum != 0 ? $sum/$count : 0;
                    $ab++;          
                }
            }
            

            // $field_data = $field_data->whereYear('humidity_date',Carbon::now()->format('Y'));
        }

        // $field['data'] = $field_data
        //                 ->where('fields.field_code',$field_name)
        //                 ->orWhere('fields.field_name',$field_name)->get();
            $field['data'] = $field_data->where(function($query) use ($field_name){
                                            $query->where('fields.field_code',$field_name)
                                        ->orWhere('fields.field_name',$field_name);
                                    })->get();
        $field['x_values'] = $x_values;
        $field['y_values'] = $y_values;

        echo json_encode($field);
    }
}
