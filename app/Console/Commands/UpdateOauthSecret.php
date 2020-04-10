<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateOauthSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth_secret:update
                            {--id=: The ID of the oauth_client}
                            {--secret=: The new Secret of the oauth_client from ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update secret by keyId';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client_id     = $this->option('id');
        $client_secret = $this->option('secret');
        
        $q = \DB::table('oauth_clients')->where('id', $client_id)->update(['secret' => $client_secret]);
        if($q) echo "Client secret do ID: ".$client_id." foi alterado para ".$client_secret;
    }
}
