<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Storage;
class ProcessTwitter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $getUser;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($getUser)
    {
        $this->getUser=$getUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->getUser as $user){
            $loginController=new LoginController();
            $loginController->_user=$user;
            $text=$loginController->addFollow();
            $text.=$loginController->addTimeLine();
        }
    }
}
