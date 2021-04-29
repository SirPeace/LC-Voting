<?php

namespace App\Mail;

use App\Models\Idea;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IdeaStatusChange extends Mailable
{
    use Queueable, SerializesModels;

    public $idea;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('An idea you voted for changed the status')->markdown('mails.idea-status-change');
    }
}
