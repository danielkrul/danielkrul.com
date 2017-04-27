<?php
/**
 * Author: Daniel Krůl
 * Date: 20. 4. 2017
 * Website: http://danielkrul.com
 */
class SurveyRepository extends Repository {
	public function insertVote($data) {
		$this->connection->table('survey')
		->insert($data);
	}

	public function fetchDoneVotes($surveyID, $currentIP) {
		return $this->connection->table('survey')
		->where('surveyID = ?', $surveyID)
		->where('ip = ?', $currentIP);
	}

	public function updateDoneVote($id, $data) {
		$this->connection->table('survey')
		->where('surveyID = ?', $id)
		->update($data);
	}
}