<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\Fields;
use App\Models\ResumeForecast;
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
        $default_map = '';
        
        if($request->all())
        {
            $validate = Validator::make($request->all(),[
                'date' => 'required',
                'field_name' => 'required'
            ])->validate();
            // $requested_date = $request->date;
            // $requested_field_name = $request->field_name;
            $date = $request->date;
            $field_name = $request->field_name;
            $field_data = Fields::join('pins','fields.id','pins.field_id')
                                ->join('pin_humidities','pins.id','pin_humidities.pin_id')
                                ->where('pins.humidity_date','like',$date.'%')
                                ->where(function($query) use ($field_name){
                                        $query->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                                })
                                ->get();

                                // dd($date,$field_name);
            if(count($field_data) == 0)
            {
                $default_map = Fields::select('map_file')
                                        ->where('fields.field_code',$field_name)
                                        ->orWhere('fields.field_name',$field_name)
                                        ->first();
            }
        }
        return view('check-data',compact('field_data','requested_date','requested_field_name','default_map'));
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

    public function exportCSV_forecast(Request $request)
    {
        $fileName = 'Forecast'.date('y_m_d').'.csv';
        $tasks =  Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                        ->select('past_forecasts.*','fields.field_name','fields.field_code','fields.field_alias','fields.razon_social')
                        ->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Date','Field Name', 'Code For Field', 'Alias', 'Razon Social','Humidity', 'Is Forecast');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['humidity_date']  = date("M d,Y",strtotime($task->humidity_date));
                $row['field_name']    = $task->field_name;
                $row['field_code']    = $task->field_code;
                $row['field_alias']  = $task->field_alias;
                $row['razon_social']  = $task->razon_social;
                $row['humidity']  = $task->humidity;
                $row['is_forecast']  = $task->is_forecast == 1 ? "Yes" : 'No' ;

                fputcsv($file, array($row['humidity_date'], $row['field_name'], $row['field_code'], $row['field_alias'], $row['razon_social'],$row['humidity'],$row['is_forecast']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCSV_resume(Request $request)
    {
        $fileName = 'Forecast'.date('y_m_d').'.csv';
        $tasks =  Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                        ->select('past_forecasts.*','fields.field_name','fields.field_code','fields.field_alias','fields.razon_social')
                        ->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Date','Field Name', 'Code For Field', 'Alias2', 'Razon Social','Humidity', 'Is Forecast');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['humidity_date']  = date("M d,Y",strtotime($task->humidity_date));
                $row['field_name']    = $task->field_name;
                $row['field_code']    = $task->field_code;
                $row['field_alias']  = $task->field_alias;
                $row['razon_social']  = $task->razon_social;
                $row['humidity']  = $task->humidity;
                $row['is_forecast']  = $task->is_forecast == 1 ? "Yes" : 'No' ;

                fputcsv($file, array($row['humidity_date'], $row['field_name'], $row['field_code'], $row['field_alias'], $row['razon_social'],$row['humidity'],$row['is_forecast']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function resume(Request $request)
    {
        
        $field_data = Fields::join('resume_forecast','fields.id','resume_forecast.field_id');
        


        
        $field['data'] = $field_data->get();
        

        echo json_encode($field);
    }

    public function graph_filter(Request $request)
    {
        $filter_value = $request->filter_value;
        $field_name = $request->field_name;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $field_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id');
        $x_values = [];
        $y_values = [];
        $is_forecast = [];
        $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June","July", "Aug", "Sep", "Oct", "Nov", "Dec"];

        if(!empty($from_date) && !empty($to_date))
        {
            $dateMonthYearArr = array();
            $st_dateTS = strtotime($from_date);
            $ed_dateTS = strtotime($to_date);

            for ($currentDateTS = $st_dateTS; $currentDateTS <= $ed_dateTS; $currentDateTS += (60 * 60 * 24)) {
                $currentDateStr = date("Y-m-d",$currentDateTS);
                $dateMonthYearArr[] = $currentDateStr;
            }
            // dd($dateMonthYearArr);
            $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                            ->whereBetween('humidity_date',[$from_date,date("Y-m-d",strtotime($to_date.'+1 day'))])
                            ->where(function($query) use ($field_name){
                                    $query->where('fields.field_code',$field_name)
                                ->orWhere('fields.field_name',$field_name);
                            })->get();

            foreach($f_data as $key => $fie_data)
            {
                $x_values[] = date('d M Y',strtotime($fie_data->humidity_date));
                $y_values[] = $fie_data->humidity;
                $is_forecast[] = $fie_data->is_forecast;
            }
            // dd($is_forecast);
            $active = '';
            foreach($dateMonthYearArr as $key => $date)
            {
                if(!in_array(date('d M Y',strtotime($date)),$x_values))
                {
                    if($key == 0){
                        $active = 'active';
                        $get_date = date('d M Y',strtotime($dateMonthYearArr[$key]));
                    }
                    else
                        $get_date = date('d M Y',strtotime($dateMonthYearArr[$key-1]));

                    $insert = array_search($get_date,$x_values);
                    
                    $push_in_x = array_splice( $x_values, $insert+1, 0, date('d M Y',strtotime($date)) );
                    $push_in_y = array_splice( $y_values, $insert+1, 0, (string)0 );
                    $push_in_forecast = array_splice( $is_forecast, $insert+1, 0, (string)0 );
                }
            }

            if(!empty($active))
            {
                $arr = $y_values;
                $pos = array_search(date("Y-m-d",strtotime($x_values[0])),$dateMonthYearArr);
                $val = $y_values[0];
                $for_arr = $is_forecast;
                $for_val = $is_forecast[0];

                $y_values = array_merge(array_slice($arr, 0, $pos+1), array($val), array_slice($arr, $pos));
                array_shift($y_values);
                unset($y_values[$pos+1]);

                $is_forecast = array_merge(array_slice($arr, 0, $pos+1), array($for_val), array_slice($for_arr, $pos));
                array_shift($is_forecast);
                unset($is_forecast[$pos+1]);

                $y_values = array_values($y_values);
                $is_forecast = array_values($is_forecast);

                usort($x_values, function ($a, $b) use ($dateMonthYearArr){
                    return strtotime($a) - strtotime($b);
                });
            }
            // dd($is_forecast);
        }
        else
        {
            if($filter_value == 'current_month')
            {
                $current_month_last = Carbon::now()->endOfMonth()->format('d');
                $dateMonthYearArr = [];

                for($i=1;$i<=$current_month_last;$i++)
                {
                    $i = strlen($i) == 1 ? "0".$i : $i;
                    $dateMonthYearArr[] = Carbon::now()->format('Y-m').'-'.$i;
                }
                
                $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                ->whereMonth('humidity_date', Carbon::now()->month)
                                ->where(function($query) use ($field_name){
                                        $query->where('fields.field_code',$field_name)
                                    ->orWhere('fields.field_name',$field_name);
                                })->get();

                foreach($f_data as $key => $fie_data)
                {
                    $x_values[] = date('d M Y',strtotime($fie_data->humidity_date));
                    $y_values[] = $fie_data->humidity;
                    $is_forecast[] = $fie_data->is_forecast;
                }

                $active = '';
                foreach($dateMonthYearArr as $key => $date)
                {
                    if(!in_array(date('d M Y',strtotime($date)),$x_values))
                    {
                        if($key == 0){
                            $active = 'active';
                            $get_date = date('d M Y',strtotime($dateMonthYearArr[$key]));
                        }
                        else
                            $get_date = date('d M Y',strtotime($dateMonthYearArr[$key-1]));

                        $insert = array_search($get_date,$x_values);
                        
                        $push_in_x = array_splice( $x_values, $insert+1, 0, date('d M Y',strtotime($date)) );
                        $push_in_y = array_splice( $y_values, $insert+1, 0, (string)0 );
                        $push_in_forecast = array_splice( $is_forecast, $insert+1, 0, (string)0 );
                    }
                }

                if(!empty($active))
                {
                    $arr = $y_values;
                    $pos = array_search(date("Y-m-d",strtotime($x_values[0])),$dateMonthYearArr);
                    $val = $y_values[0];

                    $for_arr = $is_forecast;
                    $for_val = $is_forecast[0];

                    $y_values = array_merge(array_slice($arr, 0, $pos+1), array($val), array_slice($arr, $pos));
                    array_shift($y_values);
                    unset($y_values[$pos+1]);

                    $is_forecast = array_merge(array_slice($arr, 0, $pos+1), array($for_val), array_slice($for_arr, $pos));
                    array_shift($is_forecast);
                    unset($is_forecast[$pos+1]);

                    $y_values = array_values($y_values);
                    $is_forecast = array_values($is_forecast);


                    usort($x_values, function ($a, $b) use ($dateMonthYearArr){
                        return strtotime($a) - strtotime($b);
                    });
                }
            }

            if($filter_value == 'next_month')
            {
                $last_date = Carbon::now()->addMonth()->endOfMonth()->format('d');
                $dateMonthYearArr = [];
                for($i=1;$i<=$last_date;$i++)
                {
                    $i = strlen($i) == 1 ? "0".$i : $i;
                    $dateMonthYearArr[] = Carbon::now()->addMonth()->format('Y-m').'-'.$i;
                }
                
                if(Carbon::now()->addMonth()->month == '12')
                {
                    // $field_data = $field_data->whereYear('humidity_date',Carbon::now()->addYear()->year)
                    //                         ->whereMonth('humidity_date', Carbon::now()->addMonth()->month);

                    $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereYear('humidity_date',Carbon::now()->addYear()->year)
                                    ->whereMonth('humidity_date', Carbon::now()->addMonth()->month)
                                    ->where(function($query) use ($field_name){
                                            $query->where('fields.field_code',$field_name)
                                        ->orWhere('fields.field_name',$field_name);
                                    })->get();
                }
                else
                {
                    // $field_data = $field_data->whereMonth('humidity_date', Carbon::now()->addMonth()->month);
                    $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                    ->whereMonth('humidity_date', Carbon::now()->addMonth()->month)
                                    ->where(function($query) use ($field_name){
                                            $query->where('fields.field_code',$field_name)
                                        ->orWhere('fields.field_name',$field_name);
                                    })->get();

                    foreach($f_data as $key => $fie_data)
                    {
                        $x_values[] = date('d M Y',strtotime($fie_data->humidity_date));
                        $y_values[] = $fie_data->humidity;
                        $is_forecast[] = $fie_data->is_forecast;
                    }

                    $active = '';
                    foreach($dateMonthYearArr as $key => $date)
                    {
                        if(!in_array(date('d M Y',strtotime($date)),$x_values))
                        {
                            if($key == 0){
                                $active = 'active';
                                $get_date = date('d M Y',strtotime($dateMonthYearArr[$key]));
                            }
                            else
                                $get_date = date('d M Y',strtotime($dateMonthYearArr[$key-1]));

                            $insert = array_search($get_date,$x_values);
                            
                            $push_in_x = array_splice( $x_values, $insert+1, 0, date('d M Y',strtotime($date)) );
                            $push_in_y = array_splice( $y_values, $insert+1, 0, (string)0 );
                            $push_in_forecast = array_splice( $is_forecast, $insert+1, 0, (string)0 );
                        }
                    }

                    if(!empty($active))
                    {
                        $arr = $y_values;
                        $pos = array_search(date("Y-m-d",strtotime($x_values[0])),$dateMonthYearArr);
                        $val = $y_values[0];

                        $for_arr = $is_forecast;
                        $for_val = $is_forecast[0];

                        $y_values = array_merge(array_slice($arr, 0, $pos+1), array($val), array_slice($arr, $pos));
                        array_shift($y_values);
                        unset($y_values[$pos+1]);

                        $is_forecast = array_merge(array_slice($arr, 0, $pos+1), array($for_val), array_slice($for_arr, $pos));
                        array_shift($is_forecast);
                        unset($is_forecast[$pos+1]);

                        $y_values = array_values($y_values);
                        $is_forecast = array_values($is_forecast);

                        usort($x_values, function ($a, $b) use ($dateMonthYearArr){
                            return strtotime($a) - strtotime($b);
                        });
                    }
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
                                        ->where(function($query) use ($field_name){
                                                $query->where('fields.field_code',$field_name)
                                            ->orWhere('fields.field_name',$field_name);
                                        });
                        $count = $f_data->count();
                        $sum = $f_data->sum('humidity');
                        $x_values[] = $monthName.' '.Carbon::now()->subYear()->year;
                        $count_array[$i] = $count;
                        $sum_array[$i] = $sum;
                        $y_values[$ab] = $sum != 0 ? $sum/$count : 0;      
                        $ab++;              
                    }

                    for($i=1;$i<=$end_to;$i++)
                    {
                        $monthName = date('F', mktime(0, 0, 0, $i, 10)); 
                        $x_values[] = $monthName.' '.Carbon::now()->format('Y');
                        $f_data = Fields::join('past_forecasts','fields.id','past_forecasts.field_id')
                                        ->whereYear('humidity_date',Carbon::now()->year)
                                        ->whereMonth('humidity_date',$i) 
                                        ->where(function($query) use ($field_name){
                                                $query->where('fields.field_code',$field_name)
                                            ->orWhere('fields.field_name',$field_name);
                                        });
                        $count = $f_data->count();
                        $sum = $f_data->sum('humidity');
                        $count_array[$i] = $count;
                        $sum_array[$i] = $sum;
                        $y_values[$ab] = $sum != 0 ? $sum/$count : 0;
                        $ab++;              
                    }
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
                                        ->where(function($query) use ($field_name){
                                                $query->where('fields.field_code',$field_name)
                                            ->orWhere('fields.field_name',$field_name);
                                        });
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
                                        ->where(function($query) use ($field_name){
                                                $query->where('fields.field_code',$field_name)
                                            ->orWhere('fields.field_name',$field_name);
                                        });
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
                                        ->where(function($query) use ($field_name){
                                                $query->where('fields.field_code',$field_name)
                                            ->orWhere('fields.field_name',$field_name);
                                        });
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
        }

        
            $field['data'] = $field_data->where(function($query) use ($field_name){
                                            $query->where('fields.field_code',$field_name)
                                        ->orWhere('fields.field_name',$field_name);
                                    })->get();
        $field['x_values'] = $x_values;
        $field['y_values'] = $y_values;
        $field['is_forecast'] = $is_forecast;

        echo json_encode($field);
    }
}
