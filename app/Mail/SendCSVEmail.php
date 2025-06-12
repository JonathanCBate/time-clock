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
        return $this->subject('Work Time CSV Report for ' . date('d-m-Y'))
                    ->view('emails.CSV_report') // create this view for body
                    ->attach($this->csvPath, [
                        'as' => 'work_time_report' . date('d-m-Y') . '.csv',
                        'mime' => 'text/csv',
                    ]);
    }
}

