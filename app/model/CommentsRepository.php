<?php
/**
 * Author: Daniel Krůl
 * Date: 20. 4. 2017
 * Website: http://danielkrul.com
 */
class CommentsRepository extends Repository {
	public function fetchArticleComments($post_id) {
		return $this->connection->table('comments')
		->where('post_id = ?', $post_id);
	}
	
	public function insert($data) {
		$this->connection->table('comments')
		->insert($data);
	}

	public function deleteComments($id) {
		$this->connection->table('comments')
		->where('post_id = ?', $id)
		->delete();
	}
}