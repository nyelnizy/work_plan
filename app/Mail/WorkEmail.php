<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $from_email,$subject_of_email,$attachments_of_email;

    public $content;

    /**
     * Create a new message instance.
     *
     * @param $from
     * @param $subject
     * @param $content
     * @param $attachments
     */
    public function __construct($from, $subject, $content,$attachments)
    {
        $this->from_email = $from;
        $this->subject_of_email = $subject;
        $this->content = $content;
        $this->attachments_of_email = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from($this->from_email,config('wp.from_name'))
            ->subject($this->subject_of_email)
            ->markdown('work_plan');
        foreach ($this->attachments_of_email as $attachment){
            $mail->attachData($attachment['content'],$attachment['name']);
        }
        return $mail;
    }
}
