<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use VK\VK;

class VkPublisher extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'vk:publisher';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish post from post directory.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

    private function getAllFilesNoDots($path) {
        return array_values(array_filter(scandir($path), function($item) use ($path) {
            if(in_array($item, ['.', '..'])) {
                return false;
            }

            return true;
            //return is_dir($path.'/'.$item);
        }));
    }

    private function getDirsNoDots($path) {
        return array_values(array_filter($this->getAllFilesNoDots($path), function($item) use ($path) {
            return is_dir($path.'/'.$item);
        }));
    }

    private function sortFilesBy1stNumber($a) {
        usort($a, function($a, $b) {
            $a = (int)$a;
            $b = (int)$b;

            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        return $a;
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        // todo fix music name
        $path = storage_path().'/app/posts';
        $a = $this->sortFilesBy1stNumber($this->getDirsNoDots($path));

        if(count($a) < 1) {
            $this->info('No dirs left');
            return ;
        }

        $toPublish = $path.'/'.$a[0];
        $b = $this->getAllFilesNoDots($toPublish);
        $message = '';
        $attachments = [];
        $musicAttachments = [];
        foreach($b as $file) {
            $path = $toPublish.'/'.$file;
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            switch($ext) {
                case 'txt':
                    $message = mb_convert_encoding(file_get_contents($path), "utf-8", "windows-1251");
                    break;
                case 'jpeg':
                case 'jpg':
                    $attachments[] = $path;
                    break;
                case 'mp3':
                    $musicAttachments[] = $path;
                    break;
            }
        }

        try {
            $vk = new VK(config('vk.app_id'), config('vk.api_secret'), config('vk.access_token'));

            if(!$vk->isAuth()) {
                throw new \Exception('Invalid auth');
            }


            $gid = config('vk.group_id');
            $postRequestToVk = [
                'owner_id' => '-'.$gid,
                'attachments' => []
            ];

            if(count($musicAttachments) > 0) {
                $b = array_map(function($musicFile) use ($vk) {
                    $response = \VkApiHelper::uploadAudio($vk, $musicFile);
                    $response = $response['response'];
                    return 'audio'.$response['owner_id'].'_'.$response['aid'];
                },$musicAttachments);

                $postRequestToVk['attachments'] = array_merge(
                    $postRequestToVk['attachments'],
                    $b
                );
            }

            if(count($attachments) > 0) {
                $attachments = array_splice($attachments, 0, 5 - count($musicAttachments));

                $res = \VkApiHelper::uploadPhoto($vk, $gid, $attachments);
                $postRequestToVk['attachments'] = array_merge(
                    $postRequestToVk['attachments'],
                    array_map(function($item) {
                        return $item['id'];
                    }, $res)
                );

            }

            $postRequestToVk['attachments'] = implode(',',$postRequestToVk['attachments']);

            if(strlen($message) > 0) {
                $postRequestToVk['message'] = $message;
            }

            $b = $vk->api('wall.post', $postRequestToVk, 'array', 'post');
            if(isset($b['response']) && isset($b['response']['post_id'])) {
                $this->info("Post SUCCESS!!!");
                $this->info("Moving dir {$toPublish} ...");
                $newPath = storage_path().'/app/processed/'.$a[0];
                rename($toPublish, $newPath);
                $this->info("New path: $newPath");
            } else {
                throw new \Exception('Invalid response');
            }

        } catch(\Exception $e) {
            $this->error("Processing failed. ".$e->getMessage());

            $this->info("Moving dir {$toPublish} ...");
            $newPath = storage_path().'/app/failed/'.$a[0];
            rename($toPublish, $newPath);
            $this->info("New path: $newPath");
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
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
