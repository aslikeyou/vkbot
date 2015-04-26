<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DropboxTest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dropbox:test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
	public function fire()
	{

		$client = new \Dropbox\Client(config('dropbox.access_token'), 'VKBOT/1.0');
		$response = $client->getMetadataWithChildren('/vkposts');
		if(!isset($response['contents'][0]['parent_shared_folder_id'])) {
			throw new Exception("Error Processing Request", 1);
		}

		foreach ($response['contents'] as $value) {
			if (!$value['is_dir']) {
				// todo add errors logging
				continue;
			}

			$childResponse = $client->getMetadataWithChildren($value['path']);

			if(!isset($childResponse['contents'][0]['parent_shared_folder_id'])) {
				continue;
			}

			$savePath = storage_path().'/app/posts/'.str_replace('/vkposts/', '', $value['path']);
			mkdir($savePath);
			$fileToSave = [];
			foreach ($childResponse['contents'] as $file) {
				if($file['is_dir']) {
					continue ;
				}

				$fileToSave[$file['path']] = $savePath.str_replace($value['path'], '', $file['path']);
			}

			if(count($fileToSave) > 0) {
				foreach ($fileToSave as $k => $filePath) {
					$fd = fopen($filePath, "wb");
	     			$metadata = $client->getFile($file['path'], $fd);
	      			if(fclose($fd) !== true) {
	      				throw new Exception("Can not save file", 1);
	      			}
	      			
      				unset($fileToSave[$k]);
				}
			}
			if(count($fileToSave) === 0) {
				$tres2 = $client->delete($value['path']);
				if(!isset($tres2['is_deleted']) && $tres2['is_deleted'] !== true) {
  					throw new Exception("Can not delete dir", 1);
  				}
			}
		}
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
