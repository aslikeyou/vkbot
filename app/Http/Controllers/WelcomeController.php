<?php namespace App\Http\Controllers;


use App\Attachment;
use App\MyGroup;
use App\WallPost;
use getjump\Vk\Auth;
use getjump\Vk\Core;
use Illuminate\Http\Request;

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

	public function setUpMyGroups(Core $vk) {
		$a = $vk->request('groups.get', [
			'extended' => '1',
			'filter' => 'moder'
		])->fetchData()->items;

		$res = MyGroup::insertOrUpdate(array_map(function($row) {
			return array_only((array)$row, ['id', 'name', 'screen_name']);
		}, $a));

		dd($res);
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

		if($token === null) {
			$tokenFromForm = $request->input('token');
			if($tokenFromForm !== null) {
				\Cache::forever('token', $tokenFromForm);
				dd("Token set to ".$tokenFromForm);
			}

			$auth = Auth::getInstance();
			$auth->setAppId('4865330')
				->setScope('wall,friends,offline,photos,audio')
				->setSecret('L8lsIl6koOlf3aM4Lh5j')
				->setRedirectUri('https://oauth.vk.com/blank.html');

			return view('index', [
				'url' => str_replace('response_type=code', 'response_type=token',$auth->getUrl())
			]);
		}

		return $token;
	}

}
