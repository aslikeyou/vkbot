<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class VkRemoveUsers extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'vk:deldogs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove banned users.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

    private function arrayToString(array $a) {
        return print_r($a, true);
    }


    private function offsetProccesor($count, $cb,$perPage = 1000, $offset = 0) {
        //0-999
        $b = [];
        for($i = $offset; $i <= ((int)$count/$perPage) * $perPage ;$i+= $perPage) {
            $b = array_merge($b, call_user_func($cb, $count, $perPage, $i));
        }
        return $b;
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$vk = \VkApiHelper::getI();
        $res = $vk->api('groups.getMembers', [
           'group_id' => \VkApiHelper::getCurrentGroup(),
           'fields' => 'deactivated'
        ]);
      
      

        $goNext = isset($res['response']['users'])
            && isset($res['response']['count']);


        if(!$goNext) {
            throw new \Exception('Vk error. '.$this->arrayToString($res));
        }
        // 3550

        // n = 1000
        // offset = 0
        $count = $res['response']['count'];
        $users = $res['response']['users'];
    	$perPage = 1000;
        $ololo = $this->offsetProccesor($count, function($count, $perPage, $offset) use ($vk) {
            $res = $vk->api('groups.getMembers', [
           		'group_id' => \VkApiHelper::getCurrentGroup()
           		, 'offset' => $offset
           		, 'fields' => 'deactivated'
        	]);

        	return $res['response']['users'];
        }, $perPage, $perPage);
        $users = array_merge($users, $ololo);
        $b = array_filter($users, function($item) {
    		return isset($item['deactivated']);
        });

        echo count($b).PHP_EOL;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
		//	['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
		//	['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
