<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCSVEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $csvPath;

    public function __construct($csvPath)
    {
        $this->csvPath = $csvPath;
    }

    public function build()
    {
        return $this->subject('Work Time CSV Report for ' . date('Y-m-d'))
                    ->view('emails.CSV_report') // create this view for body
                    ->attach($this->csvPath, [
                        'as' => 'work_time_report_for ' . date('Y-m-d') . '.csv',
                        'mime' => 'text/csv',
                    ]);
    }
}

