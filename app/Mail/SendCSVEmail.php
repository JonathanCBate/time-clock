<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendCSVEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $csvPath;

    public function __construct($csvPath)
    {
        $this->csvPath = $csvPath;
    }

    public function build()
    {
        return $this->subject('Work Time CSV Report')
                    ->view('emails.CSV_report') // create this view for body
                    ->attach($this->csvPath, [
                        'as' => 'work_time_report.csv',
                        'mime' => 'text/csv',
                    ]);
    }
}

