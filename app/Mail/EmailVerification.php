<?php
namespace App\Mail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
  use Queueable, SerializesModels;

  protected $data;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {

    // Create verification url
    $verifyUrl = \URL::temporarySignedRoute(
        'verification.verify',
        Carbon::now()->addMinutes(\Config::get('auth.verification.expire', 60)),
        [
          'id' => $this->data['user']->id,
          'hash' => sha1($this->data['user']->email)
        ]
    );

    // Send mail
    $mail = $this->subject('Ihre Registration')
                 ->with(
                    [
                      'user' => $this->data['user'],
                      'verifyUrl' => $verifyUrl,
                      'user_data' => $this->data['user_data']
                    ]
                  )
                 ->markdown('mails.user.verification');
    return $mail;
  }
}
