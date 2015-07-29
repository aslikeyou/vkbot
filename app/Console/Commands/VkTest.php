<?php

namespace App\Console\Commands;

use App\Vk\NewsFeed;
use Illuminate\Console\Command;

class VkTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:test {source_group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run test command.';

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

        $vk = \VkApiHelper::getI();
        $uid         = -1 * $this->argument('source_group');

        $firstFilteredItem = NewsFeed::getLastPostFromGroup($vk, $uid);

        dd($firstFilteredItem);
    }
}
