<?php

namespace T3ac\Person\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use T3ac\Person\Domain\Model\Dto\SearchFormDto;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PersonRepository extends Repository
{
    /**
     * In TYPO3 13 nutzen wir Constructor Injection.
     * Wir injizieren den ConnectionPool und die QuerySettings direkt.
     */
    public function __construct(
        protected readonly ConnectionPool $connectionPool,
        Typo3QuerySettings $querySettings
        ) {
            parent::__construct();
            
            // Standardmäßig Storage-Page ignorieren (wie in deinem initializeObject)
            $querySettings->setRespectStoragePage(false);
            $this->setDefaultQuerySettings($querySettings);
    }
    

    public function findAll(): \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        $query->setOrderings([
            'area' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            'department' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            'position' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
            
            'lastName' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            'firstName' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ]);
        return $query->execute();
    }

    public function findByUids(array $uids): iterable
    {
        $query = $this->createQuery();
        $query->matching($query->in('uid', $uids));
        // Falls du nach Name sortieren willst (wie in deinem Code)
        $query->setOrderings(['lastName' => QueryInterface::ORDER_ASCENDING]);
        return $query->execute();
    }
    
    public function findByDepartment(string $department): iterable
    {
        $query = $this->createQuery();
        $query->matching($query->equals('department', $department));
        return $query->execute();
    }
    
    public function findByArea(string $area): iterable
    {
        $query = $this->createQuery();
        $query->matching($query->equals('area', $area));
        return $query->execute();
    }

    
    /**
     * Liefert dynamisch nur Buchstaben, zu denen echte Datensätze existieren
     */
    public function getFirstChars(string $pages = ''): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('fe_users');
        $query = $queryBuilder
        ->selectLiteral('LOWER(SUBSTR(last_name, 1, 1)) AS firstchar')
        ->from('fe_users')
        ->where($queryBuilder->expr()->neq('last_name', $queryBuilder->createNamedParameter('')))
        ->groupBy('firstchar')
        ->orderBy('firstchar', 'ASC');
        
        $pids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $pages, true);
        if (!empty($pids)) {
            $query->andWhere($queryBuilder->expr()->in('pid', $queryBuilder->createNamedParameter($pids, Connection::PARAM_INT_ARRAY)));
        }
        
        $rows = $query->executeQuery()->fetchAllAssociative();
        return array_column($rows, 'firstchar');
    }
    
    /**
     * Kombinierte Suche (Freitext + ABC)
     */
    public function findSearchForm(SearchFormDto $searchFormDto, string $pages = '')
    {
        $query = $this->createQuery();
        $constraints = [];
        
        if ($searchFormDto->getSearchWord() !== '') {
            $search = '%' . $searchFormDto->getSearchWord() . '%';
            $constraints[] = $query->logicalOr(
                $query->like('username', $search),
                $query->like('lastName', $search),
                $query->like('firstName', $search),
                $query->like('department', $search)
                );
        }
        
        if ($searchFormDto->getFirstChar() !== '') {
            $constraints[] = $query->like('lastName', $searchFormDto->getFirstChar() . '%');
        }
        
        $pids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $pages, true);
        if (!empty($pids)) {
            $constraints[] = $query->in('pid', $pids);
        }
        
        if ($constraints !== []) {
            $query->matching($query->logicalAnd(...$constraints));
        }
        
        $query->setOrderings(['lastName' => QueryInterface::ORDER_ASCENDING, 'firstName' => QueryInterface::ORDER_ASCENDING]);
        return $query->execute();
    }
    
    public function findAllName(string $wo)
    {
        $query = $this->createQuery();
        $pids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $wo, true);
        if (!empty($pids)) {
            $query->matching($query->in('pid', $pids));
        }
        $query->setOrderings(['lastName' => QueryInterface::ORDER_ASCENDING]);
        return $query->execute();
    }
    
    /**
     * Erzeugt ein leeres QueryResultInterface-Objekt für den Paginator
     */
    public function createEmptyResult(): \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        // Ein unmöglicher Constraint (UID = 0) erzwingt ein leeres, typkorrektes Ergebnis
        return $query->matching($query->equals('uid', 0))->execute();
    }
}
