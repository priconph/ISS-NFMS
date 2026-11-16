<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CSVDiskImport;
use App\Mail\SendNFMSMail;
use Mail;
use Carbon\Carbon;
use App\Jobs\SendNFMSMailJob;

class DiskController extends Controller
{
    //

    public function upload_csv(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        // $collections = Excel::toCollection(new CSVDiskImport, request()->file('csv_file'));

        // $collections = Excel::toCollection(new CSVDiskImport, realpath("C:\Users\apbautista\Downloads\Network Disk Usage 20200303.csv"));

        $path = "";
        $file = "";

        $year = date('Y');
        $month = date('m');
        $day = date('d');

    	// For Manual
        if(isset($request->csv_file)){
        	$file = request()->file('csv_file');
        }
    	// For Automatic
        else{
        	$year = date('Y');
        	$month = date('m');
        	$day = date('d');
	        // $path = "\\\\raijin\Raijin Systems\ENSDM\Network Disk Usage " . $year . $month . $day . ".csv";
            $path = "/var/www/ENSDM/Network Disk Usage " . $year . $month . $day . ".csv";
            // return $path;
	        $file = realpath($path);
	        // return $path;
        }

        if(file_exists($file)){
	        try{
		        $collections = Excel::toCollection(new CSVDiskImport, $file);

		        // return request()->file('csv_file');
		        // return $collections;

                $arr_warning = [];
                $arr_full_space = [];

		        $data = [];
	            for($index = 1; $index < count($collections[0]); $index++){
                    $free = number_format(doubleval($collections[0][$index][2]) - doubleval($collections[0][$index][3]), 2, '.', '');
                    $percentage = number_format((doubleval($collections[0][$index][3]) / doubleval($collections[0][$index][2]) * 100), 2, '.', '');


                    $description = $collections[0][$index][0];

                    // if($description == null){
                    //     $description = "EMS";
                    // }

	                $data[] = [
	                	'description' => $description,
	                	'path' => $collections[0][$index][1],
	                	'size' => $collections[0][$index][2],
	                	'usage' => $collections[0][$index][3],
                        'free' => $free,
                        'percentage' => $percentage,
	                ];

                    if($percentage >= 85){
                        $arr_warning[] = [
                            'description' => $description,
                            'path' => $collections[0][$index][1],
                            'size' => $collections[0][$index][2],
                            'usage' => $collections[0][$index][3],
                            'free' => $free,
                            'percentage' => $percentage,
                        ];
                    }
	            }

                $subject = "ISS Network Folder Monitoring System - " . Carbon::now()->isoFormat('lll');
                $send_nfms_mail = new SendNFMSMail($data, $arr_warning, $arr_full_space, $subject);
                // $attention_to = [
                //     'apbautista@pricon.ph',
                //     'jnpineda@pricon.ph',
                // ];
                
                // if(count($arr_warning) > 0){
                //     $attention_to[] = 'servicerequest@pricon.ph';
                // }

                // $attention_to = [
                //     'apbautista@pricon.ph',
                // ];
                // Mail::to($attention_to)->send($send_nfms_mail);


	            return response()->json(['result' => 1, 'data' => $data, 'arr_warning' => $arr_warning, 'arr_full_space' => $arr_full_space]);
	        }    
	        catch(\Exception $e) {
	            return response()->json(['result' => 0, 'data' => [], 'data' => [], 'data' => [], 'error' => $e]);
	        }
        }
        else{
        	return response()->json(['result' => 0, 'data' => [], 'data' => [], 'data' => [], 'error' => 'File not exist!']);
        }

    }

    public function automatic_email(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        dispatch(new SendNFMSMailJob());

        // return 'Success';
        $path = "";
        $file = "";

        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $path = "/var/www/ENSDM/Network Disk Usage " . $year . $month . $day . ".csv";
        $file = realpath($path);

        if(file_exists($file)){
            try{
                $collections = Excel::toCollection(new CSVDiskImport, $file);

                $arr_warning = [];
                $arr_full_space = [];

                $data = [];
                for($index = 1; $index < count($collections[0]); $index++){
                    $free = number_format(doubleval($collections[0][$index][2]) - doubleval($collections[0][$index][3]), 2, '.', '');
                    $percentage = number_format((doubleval($collections[0][$index][3]) / doubleval($collections[0][$index][2]) * 100), 2, '.', '');

                    $description = $collections[0][$index][0];

                    // if($description == null){
                    //     $description = "EMS";
                    // }

                    $data[] = [
                        'description' => $description,
                        'path' => $collections[0][$index][1],
                        'size' => $collections[0][$index][2],
                        'usage' => $collections[0][$index][3],
                        'free' => $free,
                        'percentage' => $percentage,
                    ];

                    if($percentage >= 85){
                        $arr_warning[] = [
                            'description' => $collections[0][$index][0],
                            'path' => $collections[0][$index][1],
                            'size' => $collections[0][$index][2],
                            'usage' => $collections[0][$index][3],
                            'free' => $free,
                            'percentage' => $percentage,
                        ];
                    }
                }

                $subject = "ISS Network Folder Monitoring System - " . Carbon::now()->isoFormat('lll');
                $send_nfms_mail = new SendNFMSMail($data, $arr_warning, $arr_full_space, $subject);
                $attention_to = [
                    // 'teamiss@pricon.ph',
                    'group-iss@pricon.ph',
                ];
                
                if(count($arr_warning) > 0){
                    $attention_to[] = 'servicerequest@pricon.ph';
                }

                // $attention_to = [
                //     'apbautista@pricon.ph',
                // ];
                Mail::to($attention_to)->send($send_nfms_mail);


                return response()->json(['result' => 1, 'data' => $data, 'arr_warning' => $arr_warning, 'arr_full_space' => $arr_full_space]);
            }    
            catch(\Exception $e) {
                return response()->json(['result' => 0, 'data' => [], 'data' => [], 'data' => [], 'error' => $e]);
            }
        }
        else{
            return response()->json(['result' => 0, 'data' => [], 'data' => [], 'data' => [], 'error' => 'File not exist!']);
        }

    }
}
