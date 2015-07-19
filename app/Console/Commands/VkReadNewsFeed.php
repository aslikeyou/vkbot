<?php namespace App\Console\Commands;

use App\Exceptions\AlreadyExistException;
use App\Exceptions\InvalidResponseException;
use App\VkActivity;
use Illuminate\Console\Command;
use VK\VK;

class VkReadNewsFeed extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'vk:newsfeed {source_group} {target_group}';

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

    protected function downloadPhoto($originalFileUrl)
    {
        $saveDir  = storage_path() . "/vkimages";
        $ext      = pathinfo($originalFileUrl, PATHINFO_EXTENSION);
        $tmpfname = $saveDir . '/' . time() . '_' . rand() . '.' . $ext;
        $img      = \Image::make($originalFileUrl);
        $img->save($tmpfname);

        return $tmpfname;
    }

    protected function getLastPostFromGroup(VK $vk, $uid) {
        $newsfeedGetresponse = $vk->api('newsfeed.get', $reqParams = [
            'source_ids' => $uid,
            'filter'     => 'post',
            //'count'    => 5,
        ]);

        if ( ! isset($newsfeedGetresponse['response']['items'])) {
            throw new InvalidResponseException(
                'newsfeed.get'
                , $reqParams
                , $newsfeedGetresponse
            );
        }

        $fileteredItems
            = array_filter($newsfeedGetresponse['response']['items'],
            function ($row) {
                return isset($row['type']) && isset($row['post_type'])
                && $row['type'] === "post"
                && $row['post_type'] === "post";
            });

        $firstFilteredItem = array_values($fileteredItems)[0];
        return $firstFilteredItem;
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
        $targetGroup = $this->argument('target_group');

        $firstFilteredItem = $this->getLastPostFromGroup($vk, $uid);

        if (VkActivity::where($whereClause = [
            'source_id' => $firstFilteredItem['source_id'],
            'post_id'   => $firstFilteredItem['post_id']
        ])->exists()
        ) {
            throw new AlreadyExistException($whereClause);
        }

        // first save all photos to tmp dir
        $filePaths = [];
        foreach ($firstFilteredItem['attachments'] as $attach) {
            // script can work only with photo now
            if ($attach['type'] !== 'photo') {
                continue;
            }

            $filePaths[] = $this->downloadPhoto($attach['photo']['src_big']);
        }

        $makePostResponse = \VkApiHelper::makePost($vk, $targetGroup,
            $firstFilteredItem['text'], $filePaths);

        if ( ! isset($makePostResponse['response']['post_id'])) {
            throw new InvalidResponseException(
                '\VkApiHelper::makePost',
                [$vk, $targetGroup, $firstFilteredItem['text'], $filePaths],
                $makePostResponse
            );
        }

        VkActivity::create([
            'source_id' => $firstFilteredItem['source_id'],
            'post_id'   => $firstFilteredItem['post_id']
        ]);
    }
}
