<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPlanExpireMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected string $plan;
    protected string $email;
    protected string $text;

    /**
     * Create a new job instance.
     */
    public function __construct($user, string $plan)
    {
        $this->user = $user;
        $this->email = $user->email;
        $this->plan = $plan;
        $this->text = "Dear $user->firstname your $plan plan in library has been expired.";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        Mail::to($this->email)->send(new \App\Mail\SendExpirePlanEmail($this->email));
        echo "sent" . PHP_EOL;
//        Mail::to($this->email)->send(new SendExpirePlanEmail($this->email));

        Notification::create([
            'user_id' => $this->user->id,
            'message' => $this->text,
            'send_type' => 'email',
            'receptor' => $this->email,
        ]);
        echo "FA" . PHP_EOL;
    }
}
