<?php

declare(strict_types=1);

namespace T3ac\Person\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use T3ac\Person\Domain\Repository\PersonRepository;
use T3ac\Person\Domain\Model\Dto\SearchFormDto;

/**
 * PersonController
 */
class PersonController extends ActionController
{
    
    /**
     * @var \T3ac\Person\Domain\Repository\PersonRepository
     */
    protected $personRepository; // Diese Zeile MUSS vorhanden sein
    
    public function __construct(\T3ac\Person\Domain\Repository\PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }
    
    /**
     * Index Action
     */
    public function indexAction(): ResponseInterface
    {
        $selectedUserUids = $this->settings['user'] ?? '';
        $selectedDept = $this->settings['department'] ?? '';
        $selectedArea = $this->settings['area'] ?? '';
        
        // 1. Daten holen
        if (!empty($selectedUserUids) && $selectedUserUids !== '0') {
            $uids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', (string)$selectedUserUids, true);
            $allItems = $this->personRepository->findByUids($uids);
        } elseif (!empty($selectedDept) && $selectedDept !== '0') {
            $allItems = $this->personRepository->findByDepartment((string)$selectedDept);
        } elseif (!empty($selectedArea) && $selectedArea !== '0') {
            $allItems = $this->personRepository->findByArea((string)$selectedArea);
        } else {
            // KORREKTUR: Ein leeres Extbase-QueryResult statt eines Arrays erzeugen
            $allItems = $this->personRepository->createEmptyResult();
        }
        
        // Wert aus der Flexform holen
        $itemsPerPage = (int)($this->settings['itemsPerPage'] ?? 10);
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 10;
        }
        
        // 2. Pagination Parameter
        $currentPage = $this->request->hasArgument('currentPage')
        ? (int)$this->request->getArgument('currentPage')
        : 1;
        
        // 3. Paginator aufbauen (stürzt jetzt nicht mehr ab)
        $paginator = new QueryResultPaginator($allItems, $currentPage, $itemsPerPage);
        $pagination = new SimplePagination($paginator);
        
        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => $pagination,
            'count' => is_countable($allItems) ? count($allItems) : 0,
        ]);
        
        return $this->htmlResponse();
    }

    
    /**
     * Action list
     */
    public function listAction(): ResponseInterface
    {
        // TYPO3 13 Best Practice: Erst prüfen, ob die Einstellung existiert und befüllt ist
        $storagePid = '';
        if (isset($this->settings['pages']) && (string)$this->settings['pages'] !== '') {
            $storagePid = trim((string)$this->settings['pages']);
        }
        
        // Harder Check: Wenn der String leer, '0' oder nicht gesetzt ist -> Liste MUSS leer sein
        if ($storagePid === '' || $storagePid === '0') {
            $personen = [];
        } else {
            // Findet nur Personen in den explizit ausgewählten Ordnern
            $personen = $this->personRepository->findAllName($storagePid);
        }
        
        // Zählen der Ergebnisse für das Fluid-Template
        $count = is_countable($personen) ? count($personen) : 0;
        
        $this->view->assignMultiple([
            'person' => $personen,
            'count' => $count
        ]);
        
        return $this->htmlResponse();
    }
    
	
	public function searchAction(string $firstChar = '', string $searchWord = ''): ResponseInterface
	{
	    $searchForm = new SearchFormDto();
	    
	    if ($searchWord === '' && $this->request->hasArgument('searchWord')) {
	        $searchWord = (string)$this->request->getArgument('searchWord');
	    }
	    
	    $searchForm->setSearchWord($searchWord);
	    if ($firstChar !== '') {
	        $searchForm->setFirstChar($firstChar);
	    }
	    
	    $pages = $this->settings['pages'] ?? '';
	    
	    // Datenabfrage
	    if ($searchWord !== '' || $firstChar !== '') {
	        $allItems = $this->personRepository->findSearchForm($searchForm, (string)$pages);
	    } else {
	        $allItems = $this->personRepository->findAllName($pages);
	    }
	    
	    // Native Core-Pagination initialisieren
	    $itemsPerPage = (int)($this->settings['itemsPerPage'] ?? 10);
	    $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
	    
	    $paginator = new QueryResultPaginator($allItems, $currentPage, $itemsPerPage);
	    $pagination = new SimplePagination($paginator);
	    
	    $this->view->assignMultiple([
	        'paginator' => $paginator,
	        'pagination' => $pagination,
	        'searchWord' => $searchWord,
	        'activeChar' => $firstChar,
	        'allChars' => $this->personRepository->getFirstChars((string)$pages),
	        'count' => is_countable($allItems) ? count($allItems) : 0,
	    ]);
	    
	    return $this->htmlResponse();
	}

	
	/**
	 * action show
	 *
	 * @param \T3ac\Person\Domain\Model\Person $person
	 */
	public function showAction(?\T3ac\Person\Domain\Model\Person $person = null): ResponseInterface
	{
	    // 1. Fall: Das Objekt wurde per Link übergeben (aus der Liste)
	    if ($person !== null) {
	        $this->view->assign('person', $person);
	    }
	    // 2. Fall: Kein Link-Parameter, schaue in die FlexForm (Einzelwahl)
	    else {
	        $personUid = (int)($this->settings['person'] ?? 0);
	        if ($personUid > 0) {
	            $person = $this->personRepository->findByUid($personUid);
	            $this->view->assign('person', $person);
	        }
	    }
	    
	    return $this->htmlResponse();
	}
	
}
