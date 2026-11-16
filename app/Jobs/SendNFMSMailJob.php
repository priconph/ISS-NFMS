<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mail;
use App\Mail\SendNFMSMail;
use App\Imports\CSVDiskImport;
use Carbon\Carbon;
use Excel;

class SendNFMSMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $data;
    public $warning_data;
    public $full_space_data;
    public $subject;
    public $attention_to;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        //
        date_default_timezone_set('Asia/Manila');

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

                    if($description == null){
                        $description = "EMS";
                    }

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
                $attention_to = [
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
