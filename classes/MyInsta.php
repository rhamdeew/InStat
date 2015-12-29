<?php

namespace MetzWeb\Instagram;

class MyInsta extends Instagram {

	public function getUserFollows($id = 'self', $limit = 0, $next_cursor = '') {
		$params = array('count' => $limit);
		if(!empty($next_cursor)) {
			$params['cursor'] = $next_cursor;
		}
		return $this->_makeCall('users/' . $id . '/follows', true, $params);
	}

	public function getUserFollower($id = 'self', $limit = 0, $next_cursor = '') {
		$params = array('count' => $limit);
		if(!empty($next_cursor)) {
			$params['cursor'] = $next_cursor;
		}
		return $this->_makeCall('users/' . $id . '/followed-by', true, $params);
	}

	public function getTagMedia($name, $limit = 0,$params = array())
	{
		if ($limit > 0) {
			$params['count'] = $limit;
		}
		return $this->_makeCall('tags/' . $name . '/media/recent', false, $params);
	}

	public function getMostLikedTagMedia($name, $limit_sort = 1000, $limit_slice = 10, $limit_seconds = 86400) {
		$result = $this->getTag($name);
		$count = $result->data->media_count;

		$i=0;
		$params = [];
		$mostLiked = [];
		$time = time();
		while($i<=$limit_sort && $i<=$count) {
			$s = 33;
			if($limit_sort<33) {
				$s = $limit_sort;
			}
			$photos = $this->getTagMedia($name,$s,$params);

			foreach ($photos->data as $key => $photo) {
				$mostLiked[$photo->likes->count][] = $photo->link;
				$i++;
				if(($time - $photo->created_time) >= $limit_seconds) {
					break 2;
				}
			}

			if(isset($photos->pagination->next_max_tag_id)) {
				$max_tag_id = $photos->pagination->next_max_tag_id;
				$params = ['max_tag_id'=>$max_tag_id];
			}
			else {
				break;
			}
		}

		krsort($mostLiked);
		$mostLiked = array_slice($mostLiked,0,$limit_slice);

		$mostLikedRealSlice = [];
		foreach($mostLiked as $photos) {
			krsort($photos);
			foreach ($photos as $photo) {
				$mostLikedRealSlice[] = $photo;
			}
		}
		return array_slice($mostLikedRealSlice,0,$limit_slice);
	}

	public function getBestOfTheBestTag($name,$limit = 5) {
		$result = $this->getTag($name);
		$count = $result->data->media_count;

		$i=0;
		$params = [];
		$mostLiked = [];
		while($i<=$count) {
			$s = 33;
			$photos = $this->getTagMedia($name,$s,$params);

			foreach ($photos->data as $key => $photo) {
				$mostLiked[$photo->likes->count][] = $photo->link;
				isset($users[$photo->user->username]) ? $users[$photo->user->username]++ : $users[$photo->user->username] = 1;
				$i++;
			}

			if(isset($photos->pagination->next_max_tag_id)) {
				$max_tag_id = $photos->pagination->next_max_tag_id;
				$params = ['max_tag_id'=>$max_tag_id];
			}
			else {
				break;
			}
		}

		krsort($mostLiked);
		$mostLiked = array_slice($mostLiked,0,$limit);

		$mostLikedRealSlice = [];
		foreach($mostLiked as $photos) {
			krsort($photos);
			foreach ($photos as $photo) {
				$mostLikedRealSlice[] = $photo;
			}
		}
		file_put_contents('../users.txt', implode("\n",array_keys($users)));
		return array_slice($mostLikedRealSlice,0,$limit);
	}
}
?>
