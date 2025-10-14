<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateTestingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-testing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for the API.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::whereEmail('jedwin@gmail.com')->delete();
        $user = User::factory()->create([
            'name' => 'jEdwin',
            'email' => 'jedwin@gmail.com'
        ]);

        $this->info('User ID: ');
        $this->line($user->id);

        $this->info('Token: ');
        $this->line($user->createToken('test')->plainTextToken);
    }
}
