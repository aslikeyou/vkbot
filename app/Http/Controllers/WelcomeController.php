<?php namespace App\Http\Controllers;


use App\Attachment;
use App\MyGroup;
use App\OtherGroup;
use App\WallPost;
use Carbon\Carbon;
use getjump\Vk\Auth;
use getjump\Vk\Core;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller {
	function __construct(Request $request)
	{
		if($request->route() === null) {
			return ;
		}
		$token = \Cache::get('token');
		$vk = Core::getInstance()->apiVersion('5.5');
		$vk->setToken($token);
		$request->route()->setParameter('vk', $vk);
	}

	public function clearToken() {
		\Cache::forget('token');
		return redirect('/');
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

	public function setUpMyGroups(Core $vk) {
		$a = $vk->request('groups.get', [
			'extended' => '1',
			'filter' => 'moder'
		])->fetchData()->items;

		$res = MyGroup::insertOrUpdate(array_map(function($row) {
			return array_only((array)$row, ['id', 'name', 'screen_name']);
		}, $a));

		return view('mygroups', [
			'groups' => MyGroup::all()
		]);
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

		// todo add more than one call
		$toReturn = $vk->request(
			'photos.saveWallPhoto'
			, $paramsSaveWallPhoto
		)->getResponse();
		$b = array_map(function($row) {
			return 'photo'.$row->owner_id.'_'.$row->id;
		},$toReturn);

		$postRequestToVk['message'] = $firstFilteredItem->text;
		$postRequestToVk['attachments'] = implode(',', $b);

		$c = $vk->request('wall.post', $postRequestToVk)->getResponse();

		return $c->post_id;
	}

	public function steal(Request $request, Core $vk) {
		$d = $request->input('steal');
		if($d !== null) {
			$res = parse_url($d['url_to_steal'], PHP_URL_QUERY);
			$id = trim($res, 'w=wall');

			$a = $vk->request('wall.getById',[
				'posts' => $id
			])->one();

			$id = $this->uploadPost($vk, $d['group_id'], $a);

		}
		return view('steal', [
			'post_id' => isset($id) ? $id : null,
			'groups' => MyGroup::all()
		]);
	}

	public function watch(Request $request, Core $vk) {
		$watch = $request->input('watch');
		if(!empty($watch)) {
			$other = $watch['other_group'];
			$my_group_id = $watch['my_group_id'];
			$vk->request('groups.getById', [
				'group_ids' => $other
			])->each(function($k, $item) {
				//OtherGroup::create($item)
				dd($item);
			});
		}
		return view('watch', [
			'otherGroups' => OtherGroup::all(),
			'myGroups' => MyGroup::all()
		]);
	}

	public function getPost(Core $vk) {
		$items = array_filter((array)$vk->request('wall.get', [
			// much better than ids
			'domain' => 'fuck_humor',
			// remove pin post
			'offset' => 1
		])->fetchData()->items, function($item) {
			if($item->post_type !== 'post') {
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

		$wallPosts = [];
		$attachments = [];
		foreach($items as $item) {
			$n = new WallPost();
			$n->id = $item->id;
			$n->text = $item->text;
			$n->comments_count = $item->comments->count;
			$n->likes_count = $item->likes->count;
			$n->reposts_count = $item->reposts->count;
			$n->post_source = $item->post_source->type;
			$n->post_type = $item->post_type;
			$n->date = $item->date;
			$wallPosts[] = $n->toArray();

			foreach($item->attachments as $row) {
				$b = new Attachment();
				$row = $row->photo;
				$b->id = $row->id;
				$b->type = 'photo';
				$b->photo_75 = $row->photo_75;
				$b->photo_130 = $row->photo_130;
				$b->photo_604 = $row->photo_604;
				$b->date = $row->date;
				$b->post_id = $row->post_id;

				$attachments[] = $b->toArray();
			}
		}


		$res1 = WallPost::insertOrUpdate($wallPosts);
		$res2 = Attachment::insertOrUpdate($attachments);
		dd($res1 && $res2);
		// here i have items only with 1 photo and maybe some text

		// todo something
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$token = \Cache::get('token');

		//return view('general');

		if($token === null) {
			$tokenFromForm = $request->input('token');
			if($tokenFromForm !== null) {
				\Cache::forever('token', $tokenFromForm);
				return redirect('/welcome');
			}

			$auth = Auth::getInstance();
			$auth->setAppId('4865330')
				->setScope('wall,friends,offline,photos,audio')
				->setSecret('L8lsIl6koOlf3aM4Lh5j')
				->setRedirectUri('https://oauth.vk.com/blank.html');

			return view('login_main', [
				'url' => str_replace('response_type=code', 'response_type=token',$auth->getUrl())
			]);
		}

		return redirect('/welcome');
	}

	public function welcome(Core $vk) {
		$data = $vk->request('users.get')->one();

		return view('index2', [
			'id' => $data->id,
			'first_name' => $data->first_name,
			'last_name' => $data->last_name
		]);
	}

}
