<?php

namespace App\Console\Commands;

use App\MyGroup;
use App\OtherGroup;
use Carbon\Carbon;
use getjump\Vk\Core;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class VkWatchAndSteal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function filterPost($items) {
        $items = array_filter($items, function($item) {
            if($item->post_type !== 'post') {
                return false;
            }

            if(isset($item->is_pinned) && $item->is_pinned === 1) {
                return false;
            }

            if(!is_array($item->attachments) || count($item->attachments) !== 1) {
                return false;
            }

            if($item->attachments[0]->type !== 'photo') {
                return false;
            }

            return true;
        });

        return $items;
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
    public function uploadPost(Core $vk, $target_id, $firstFilteredItem) {
        $filePaths = [];
        foreach ($firstFilteredItem->attachments as $attach) {
            // script can work only with photo now
            if ($attach->type !== 'photo') {
                continue;
            }

            $filePaths[] = $this->downloadPhoto($attach->photo->photo_604);
        }

        $postRequestToVk = [
            'owner_id' => $target_id*-1
        ];

        // only photos here
        // only 5 attaches max
        //$attachments = array_splice($attachments, 0, 5);

        $user_friends = $vk->request('photos.getWallUploadServer', [
            'group_id' => $target_id,
        ])->one();

        $client = new Client();
        $body = [];
        foreach($filePaths as $f) {
            $body['file' . (count($body) + 1)] = fopen($f, 'r');
        }

        $res = $client->post($user_friends->upload_url, [
            'body' => $body
        ]);

        $paramsSaveWallPhoto = array_merge([
            'group_id' => $target_id,
        ], $res->json());

        $toReturn = $vk->request(
            'photos.saveWallPhoto'
            , $paramsSaveWallPhoto
        )->getResponse();
        $b = array_map(function($row) {
            return 'photo'.$row->owner_id.'_'.$row->id;
        },$toReturn);

        $postRequestToVk['message'] = $firstFilteredItem->text;
        $postRequestToVk['attachments'] = implode(',', $b);
        /** @var \stdClass $c */
        $c = $vk->request('wall.post', $postRequestToVk)->getResponse();

        return $c->post_id;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = \Cache::get('token');
        $vk = Core::getInstance()->apiVersion('5.5');
        $vk->setToken($token);

        /** @var Collection $myGroups */
        $myGroups = MyGroup::query()->whereIn('name', ['УШИ ВЯНУТ', 'What`s Woman Love'])->get();
        $myGroups->each(function(MyGroup $myGroup) use ($vk) {
            try {
                /** @var Collection $otherGroups */
                $otherGroups = $myGroup->otherGroups;
                if($otherGroups->count() < 1) {
                    $this->warn($myGroup->name.' has no other groups');
                    return ;
                }
                /** @var OtherGroup $randomRow */
                $randomRow = $otherGroups->random(1);

                $data = $vk->request('wall.get', [
                    'domain' => $randomRow->screen_name
                ])->fetchData()->items;
                $data = $this->filterPost($data);

                usort($data, function($a, $b) {
                    $countA = $a->reposts->count;
                    $countB = $b->reposts->count;
                    if ($countA == $countB) {
                        return 0;
                    }
                    return ($countA < $countB) ? 1 : -1;
                });

                $reducedItem = array_reduce($data, function($carry, $item) use ($myGroup) {
                    if($carry !== null) {
                        return $carry;
                    }

                    $hash = hash_file('sha256',$item->attachments[0]->photo->photo_75);
                    $hashKey = $myGroup->id.'.'.$hash;
                    if(\Cache::has($hashKey)) {
                        return null;
                    }

                    \Cache::add($hashKey, 123, Carbon::now()->addDay());
                    return $item;
                });

                if($reducedItem === null) {
                    $this->warn('No records found in group '.$randomRow->name);
                    return ;
                }

                $id = $this->uploadPost($vk, $myGroup->id, $reducedItem);

                $this->info("Post: ".$id." was added to ".$myGroup->name);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        });
    }
}
