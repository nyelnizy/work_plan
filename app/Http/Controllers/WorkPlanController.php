<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceExport;
use App\Mail\WorkEmail;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class WorkPlanController extends Controller
{
    public function sendWork(Request $request)
    {
        try {
            $start = Carbon::parse($request->start_date);
            $hours = +$request->hours;
            $time_difference = config('wp.time_difference');

            $start_us = Carbon::parse($request->start_date)->subHours($time_difference);

            $is_plan = $request->work_type === "Plan";
            $time_type = $is_plan?'expected':'spent';
            $time_section = "Time $time_type $hours hours<br>";
            $time_section .= "My Time<br>";
            $st = null;
            $et = null;
            if($hours<=4){
                $st = "{$this->formatTime($start)} - {$this->formatTime($start->addHours($hours))}";
                $time_section .= "Begin : $st<br>";
            }else{
                $st = "{$this->formatTime($start)} - {$this->formatTime($start->addHours(4))}";
                $time_section .= "Begin : $st<br>";
                $time_section .= "Break : {$this->formatTime($start)} - {$this->formatTime($start->addHours(1))}<br>";

                $remaining_hours = $hours-4;
                $et = "{$this->formatTime($start)} - {$this->formatTime($start->addHours($remaining_hours))}";
                $time_section .= "Continue : $et<br><br>";
            }
            //your time
            $time_section .= "Your Time<br>";
            if($hours<=4){
                $time_section .= "Begin : {$this->formatTime($start_us)} - {$this->formatTime($start_us->addHours($hours))}<br>";
            }else{
                $time_section .= "Begin : {$this->formatTime($start_us)} - {$this->formatTime($start_us->addHours(4))}<br>";
                $time_section .= "Break : {$this->formatTime($start_us)} - {$this->formatTime($start_us->addHours(1))}<br>";

                $remaining_hours = $hours-4;
                $time_section .= "Continue : {$this->formatTime($start_us)} - {$this->formatTime($start_us->addHours($remaining_hours))}<br><br>";
            }

            $content = "$time_section$request->message_body";
            $date = $this->formatTime(Carbon::parse($request->start_date),"jS F, Y");
            $subject = "Work Status $date";
            if ($is_plan) {
                $subject = "Work Plan - $date";
            }

            $attachments = [];
            if(!empty($request->attachments)){
                foreach ($request->attachments as $attachment){
                    $attachments[] = $this->processFile($attachment);
                }
            }
            if(!json_decode($request->save_only)){
                $this->sendEmail($subject,$content,$attachments,$request->send);
            }
            $to = $from = config('wp.from');
            if(json_decode($request->send)){
                $to = config('wp.to');
            }
            if(is_null($et)){
                $tarr = explode('-',$st);
                $st = $tarr[0];
                $et = $tarr[1];
            }

            Work::create([
                'subject'=>$subject,
                'from'=>$from,
                'to'=>$to,
                'hours'=>$hours,
                'start_date'=>$request->start_date,
                'start_time'=>$st,
                'end_time'=>$et,
                'summary'=>$request->summary,
                'content'=>$content,
                'original_content'=>$request->message_body,
                'work_type'=>$request->work_type
            ]);
            return response()->json("Sent");
        }
        catch (\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getWorks(Request $request)
    {
        return response()->json(Work::where('start','>=',$request->start_date)
            ->where('end','<=',$request->end_date)
            ->get());
    }
     public function getWorkStatus(Request $request)
    {
        return response()->json(Work::where('work_type',"Status")
        ->orderBy('subject')
        ->count());
    }
    public function getWorkPlans(Request $request)
    {
        return response()->json(Work::where('work_type',"Plan")->whereMonth('created_at',now()->month)
        ->whereYear('created_at',now()->year)
            ->orderBy('subject')
            ->get());
    }
    public function generateInvoice(Request $request)
    {
        $request->validate(['start'=>'required','end'=>'required']);
        $export = new InvoiceExport($request->start,$request->end);
        $saved = Excel::store($export,'invoices/invoice.xlsx','wp', \Maatwebsite\Excel\Excel::XLSX);
        if($saved){
            return response()->json(Storage::url("invoices/invoice.xlsx"));
        }
        return response()->json("Failed",500);

    }

    private function formatTime(Carbon $date,$format="h:i A")
    {
        return $date->format($format);
    }

    private function sendEmail($subject,$content,$attachments,$send){
        $to = $from = config('wp.from');
        if(json_decode($send)){
            $to = config('wp.to');
        }
        Mail::to($to)->send(new WorkEmail($from,$subject,$content,$attachments));
    }

    private function processFile(UploadedFile $attachment){
        return ['content'=>$attachment->getContent(),'name'=>$attachment->getClientOriginalName()];
    }
}
