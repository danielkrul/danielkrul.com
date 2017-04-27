<?php

namespace FrontendModule;
use \Nette\Application\UI;
use \Nette\Utils\Json;
use \Nette\Utils\JsonException;
use \Nette\Utils\DateTime;
use Instante;


class SurveyPresenter extends BasePresenter {
	const TIME_LIMIT = 10;
	const JSON_FILE = 'surveyAnswers/survey.json';

	/** @var \SurveyRepository Metody pro práci s DB */
    private $surveyRepository;

	/** @var object Rozparsovaný JSON soubor */
	private $JSONValues;

	/** @var object|null Případně obsahuje chyby z JSON souboru */
	private $JSONErrors = NULL;

	/** @var object|null Dekódovaný JSON objekt */
	private $JSONContent = NULL;

	/** @var array Pole procent, které se poté sečtou */
	private $answersPercentage = [];

	/** @var object Obsahuje aktuální datetime */
	private $actualTime;

	/** @var string|integer IP adresa */
	private $currentIP;

	/** @inject @var \Nette\Http\Request */
    private $httpRequest;

	protected function createComponentVoteForm(){
		return new UI\Multiplier(function ($id) {
			$form = new UI\Form;
			$answers = [];
			$countPercentage = 0;
			$surveyTemp = $this->JSONContent->survey;

			for ($i = 0; $i < count($surveyTemp->questions[$id]->values); $i++) { 
				$countPercentage += $surveyTemp->questions[$id]->values[$i]->times;
			}

			for ($i = 0; $i < count($surveyTemp->questions[$id]->values); $i++) { 
				array_push($answers, $surveyTemp->questions[$id]->values[$i]->text . ' - ' . $this->getPercentage($countPercentage, $surveyTemp->questions[$id]->values[$i]->times) . '%');
			}

			$form->addRadioList('answerID', 'Počet hlasů: ' . $countPercentage, $answers)
				 ->setRequired('Vyberte odpověď!');
			$form->addHidden('surveyID', $id);
			$form->addSubmit('send', 'Hlasovat');
			$form->setRenderer(new Instante\Bootstrap3Renderer\BootstrapRenderer);
			$form->onSuccess[] = [$this, 'voteFormSubmitted'];
			return $form;
		});
	}

	public function voteFormSubmitted($form){
		$values = $form->getValues();
		$this->actualTime = new DateTime();
		
		$gettedResults = $this->surveyRepository->fetchDoneVotes($values['surveyID'], $this->currentIP)->fetch();
		if ($gettedResults) {
			if(($this->actualTime->getTimestamp() - $gettedResults['time']->getTimestamp()) / 60 >= self::TIME_LIMIT){
				$this->surveyRepository->updateDoneVote($values['surveyID'], [
					'time' => $this->actualTime
					]);
				$this->flashMessage('Váš hlas byl upraven!', 'alert-info');
				$this->redirect('Survey:');
			} else {
				$this->flashMessage('Již jste v této anketě hlasoval! Limit pro další hlasování je ' . self::TIME_LIMIT . ' minut', 'alert-danger');
			}
		} else {
			$this->JSONContent->survey->questions[$values['surveyID']]->values[$values['answerID']]->times++;
			file_put_contents(self::JSON_FILE, Json::encode($this->JSONContent, Json::PRETTY));

			$this->surveyRepository->insertVote([
				'surveyID' => $values['surveyID'], 
				'ip' => $this->currentIP, 
				'time' => $this->actualTime
			]);
			$this->flashMessage('Děkujeme za hlas!', 'alert-success');
			$this->redirect('Survey:');
		}
		
	}

	function __construct(\SurveyRepository $surveyRepository, \Nette\Http\Request $httpRequest) {

		parent::__construct();
		$this->surveyRepository = $surveyRepository;
		$this->JSONValues = file_get_contents(self::JSON_FILE);
		$this->httpRequest = $httpRequest;
		$this->currentIP = $this->httpRequest->remoteAddress;

        try {
            $this->JSONContent = Json::decode($this->JSONValues);
        } catch (JsonException $e) {
            // Ošetření výjimky
            $this->JSONErrors = $e;
        }
    }

    private function getPercentage($base, $number){
    	return round(($number / $base) * 100);
    }

    public function renderDefault() {
        if ($this->JSONErrors != NULL) {
        	dump($this->JSONErrors);
        	exit();
        }
    }

    public function getJSONCount(){
    	return count($this->JSONContent->survey->questions);
    }

    public function getJSONValue($id, $name){
    	return $this->JSONContent->survey->questions[$id]->$name;
    }
}