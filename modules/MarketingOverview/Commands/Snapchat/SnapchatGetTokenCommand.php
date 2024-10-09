<?php

namespace Modules\MarketingOverview\Commands\Snapchat;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SnapchatGetTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:snapchat:get-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for getting token';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $clientId = $this->ask('Client id');
        $queryString = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => 'https://stream.nhgdev.xyz/',
            'response_type' => 'code',
            'scope' => 'snapchat-marketing-api'
        ]);

        $this->info('https://accounts.snapchat.com/login/oauth2/authorize?' . $queryString);

        $secret = $this->ask('Secret');

        $code = $this->ask('Code');

        $response = Http::asForm()->post('https://accounts.snapchat.com/login/oauth2/access_token', [
            'client_id' => $clientId,
            'client_secret' => $secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'https://stream.nhgdev.xyz/',
        ]);

        $this->info($response->body());

        return 0;
    }
}
