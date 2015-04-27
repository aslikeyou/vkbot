<?php namespace App\Console\Commands;

use App\Exceptions\ArrException;
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
     * @throws ArrException
     * @throws \Exception
     */
	public function fire()
	{

        // todo fix music name
        $path2posts = storage_path().'/app/posts';
        $postDirectories = $this->sortFilesBy1stNumber($this->getDirsNoDots($path2posts));

        if(count($postDirectories) < 1) {
            $this->info('No dirs left');
            return ;
        }

        $firstPostDirectory = $path2posts.'/'.$postDirectories[0];
        $filesInPost = $this->getAllFilesNoDots($firstPostDirectory);
        $message = '';
        $attachments = [];
        $musicAttachments = [];
        foreach($filesInPost as $file) {
            $path2posts = $firstPostDirectory.'/'.$file;
            $ext = pathinfo($path2posts, PATHINFO_EXTENSION);

            switch($ext) {
                case 'txt':
                    $message = mb_convert_encoding(file_get_contents($path2posts), "utf-8", "windows-1251");
                    break;
                case 'jpeg':
                case 'jpg':
                    $attachments[] = $path2posts;
                    break;
                case 'mp3':
                    $musicAttachments[] = $path2posts;
                    break;
            }
        }

        $newDirPath = '';

        try {
            $vk = new VK(config('vk.app_id'), config('vk.api_secret'), config('vk.access_token'));

            if(!$vk->isAuth()) {
                throw new ArrException("Can not auth to VK", 1, null, [
                    'params' => [
                        'app_id' => config('vk.app_id'),
                        'api_secret' => config('vk.api_secret'),
                        'access_token' => config('vk.access_token')
                    ]
                ]
                );
            }
            $this->info("Login to VK success!");

            $groupId = config('vk.group_id');
            $postRequestToVk = [
                'owner_id' => '-'.$groupId,
                'attachments' => []
            ];

            if(count($musicAttachments) > 0) {
                $filesInPost = [];

                foreach ($musicAttachments as $musicFile) {
                    $this->info("Start upload audio file: ".$musicFile." ...");
                    $response = \VkApiHelper::uploadAudio($vk, $musicFile);
                    if(isset($response['error']['error_msg'])) {
                        $this->error(
                            sprintf('%s : %s'
                                , $response['error']['error_code']
                                , $response['error']['error_msg']
                            )
                        );
                        continue;
                    }

                    if(!isset($response['response']['aid'])) {
                        throw new ArrException(
                            "Can not upload audio : $musicFile"
                            , 1
                            , null
                            , [
                                'params' => $musicFile
                                ,'response' => $response
                            ]
                        );
                        
                    }
                    $this->info("End upload audio file: ".$musicFile."!");
                    $response = $response['response'];
                    $filesInPost[] = 'audio'.$response['owner_id'].'_'.$response['aid'];
                }

                $postRequestToVk['attachments'] = array_merge(
                    $postRequestToVk['attachments'],
                    $filesInPost
                );
            }

            if(count($attachments) > 0) {
                $attachments = array_splice($attachments, 0, 5 - count($musicAttachments));

                $this->info("Start upload photos...");
                var_dump($attachments);
                $res = \VkApiHelper::uploadPhoto($vk, $groupId, $attachments);
                
                $this->info("End upload photos!");

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

            $filesInPost = $vk->api('wall.post', $postRequestToVk, 'array', 'post');
            if(!isset($filesInPost['response']) || !isset($filesInPost['response']['post_id'])) {
                throw new ArrException(
                        "Can not run wall.post"
                        , 1
                        , null
                        , [
                            'params' => $postRequestToVk
                            , 'response' => $filesInPost
                        ]);
            }

            $this->info("Post SUCCESS!!!");
            $newDirPath = storage_path().'/app/processed/'.$postDirectories[0];
        } catch(ArrException $e) {
            var_dump($e->getOptions());
            throw $e;
        } catch(\Exception $e) {
            $this->error("Processing failed. ".$e->getMessage());
            $this->error($e->getFile().' : '.$e->getLine());

            $newDirPath = storage_path().'/app/failed/'.$postDirectories[0];
        } finally {
            $this->info("Moving dir {$firstPostDirectory} ...");
            $this->info("New path: $newDirPath");

            rename($firstPostDirectory, $newDirPath);

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
