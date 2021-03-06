<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands
        = [
            'App\Console\Commands\Inspire',
            'App\Console\Commands\VkReadNewsFeed',
            Commands\VkWatchAndSteal::class
        ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->call(function () {
//            $group1 = [
//                // 22741624
//                [
//                    'source_group' => '12382740', // еп       | https://vk.com/fuck_humor
//                    'target_group' => '97448590' // уши вянут | https://vk.com/ebat_fun
//                ],
//                [
//                    'source_group' => '22741624', // https://vk.com/art_fun
//                    'target_group' => '97448590'
//                ]
//            ];
//
//            $group2 = [
//                [
//                    'source_group' => '36166073', // https://vk.com/on.heels |  Ты на понтах, я на каблуках
//                    'target_group' => '89446579' // whats w love
//                ],
//                [
//                    'source_group' => '34486229', // https://vk.com/secretswoman
//                    'target_group' => '89446579' // whats w love
//                ],
//                [
//                    'source_group' => '24024157', // https://vk.com/kilo40gram
//                    'target_group' => '89446579' // whats w love
//                ],
//                [
//                    'source_group' => '40684908', // https://vk.com/womanoonline
//                    'target_group' => '89446579' // whats w love
//                ]
//            ];
//            $groups = [
//                $group1, $group2 ];
//
//            foreach($groups as $k => $g) {
//                $randKey = mt_rand(0, count($g) - 1);
//                echo "=== {$k} ===" . PHP_EOL;
//
//                try {
//                    \Artisan::call('vk:newsfeed', $g[$randKey]);
//                } catch (\Exception $e) {
//                    echo get_class($e);
//                    echo $e->getTraceAsString();
//                    echo PHP_EOL;
//                }
//            }
//        })->everyThirtyMinutes()
//            ->name('vkbot')
//            ->withoutOverlapping()
//            ->sendOutputTo(storage_path() . '/logs/vkreadnewsfeed.log');

//        $schedule
//            ->command('vk:newsfeed 12382740 97448590')
//            ->cron('*/2 * * * *')
//            ->withoutOverlapping()
//            ->sendOutputTo(storage_path() . '/logs/vkreadnewsfeed.log');
//
//        $schedule->command('vk:newsfeed 45739071 97448590')
//            ->cron('*/2 * * * *')
//            ->withoutOverlapping()
//            ->sendOutputTo(storage_path() . '/logs/vkreadnewsfeed2.log');
//
//        $schedule->command('vk:newsfeed 36166073 89446579')
//            ->cron('*/3 * * * *')
//            ->withoutOverlapping()
//            ->sendOutputTo(storage_path() . '/logs/vkreadnewsfeed3.log');
//
//        $schedule->command('vk:newsfeed 27470044 89446579')
//            ->cron('*/3 * * * *')
//            ->withoutOverlapping()
//            ->sendOutputTo(storage_path() . '/logs/vkreadnewsfeed4.log');
    }
}
