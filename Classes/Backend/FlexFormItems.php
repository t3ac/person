<?php

namespace T3ac\Person\Backend;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;


class FlexFormItems
{
    /**
     * Hilfsmethode: Macht aus Arrays oder Strings eine saubere kommagetrennte Liste
     */
    protected function ensureString(mixed $value): string
    {
        // 1. Wenn es schon ein String (oder null) ist
        if (!is_array($value)) {
            return (string)($value ?? '');
        }
        
        // 2. Wenn es die TYPO3 vDEF-Struktur ist ['vDEF' => '...']
        if (isset($value['vDEF'])) {
            return $this->ensureString($value['vDEF']);
        }
        
        // 3. Wenn es ein Array ist (z.B. mehrere UIDs aus dem group-Feld)
        // Wir filtern leere Werte und stellen sicher, dass jedes Element ein String ist
        $stringValues = array_map(function($item) {
            return is_array($item) ? $this->ensureString($item) : (string)$item;
        }, $value);
            
            return implode(',', array_filter($stringValues));
    }
    
    /**
     * Hilfsmethode für den Zugriff auf den LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
    
    
    public function getAreas(array &$params): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('fe_users');
        
        // Auch hier filtern wir idealerweise nach den gewählten PIDs
        $pages = $this->ensureString($params['row']['settings.pages'] ?? '');
        $pids = GeneralUtility::intExplode(',', $pages, true);
        
        $query = $queryBuilder
        ->select('area')
        ->from('fe_users')
        ->where($queryBuilder->expr()->neq('area', $queryBuilder->createNamedParameter('')));
        
        if (!empty($pids)) {
            $query->andWhere($queryBuilder->expr()->in('pid', $queryBuilder->createNamedParameter($pids, Connection::PARAM_INT_ARRAY)));
        }
        
        $rows = $query->groupBy('area')
        ->orderBy('area', 'ASC') // Alphabetisch sortieren
        ->executeQuery()
        ->fetchAllAssociative();
        
        // Das Label aus der XLF laden
        $labelAllArea = $this->getLanguageService()->sL(
            'LLL:EXT:person/Resources/Private/Language/locallang_db.xlf:flexform.all_area'
            );
                
        $params['items'][] = [$labelAllArea, '0'];
        foreach ($rows as $row) {
            $params['items'][] = [$row['area'], $row['area']];
        }
    }
    
    public function getDepartments(array &$params): void
    {
        
        $selectedArea = $this->ensureString($params['row']['settings.area'] ?? '');
        
        // Das Label aus der XLF laden
        $labelAreaFirst = $this->getLanguageService()->sL(
            'LLL:EXT:person/Resources/Private/Language/locallang_db.xlf:flexform.area_first'
            );
        
        // Wenn keine Area gewählt ist, geben wir eine leere Liste zurück
        if (empty($selectedArea) || $selectedArea === '0') {
            $params['items'][] = [$labelAreaFirst, '0'];
            return;
        }
        
        $row = $params['row'];
        
        // 1. Werte aus der FlexForm sicher abrufen
        $pages = $this->ensureString($row['settings.pages'] ?? '');
        $selectedArea = $this->ensureString($row['settings.area'] ?? '');
        
        $pids = GeneralUtility::intExplode(',', $pages, true);
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('fe_users');
        
        $query = $queryBuilder
        ->select('department')
        ->from('fe_users')
        ->where($queryBuilder->expr()->neq('department', $queryBuilder->createNamedParameter('')));
        
        // 2. Filter: Nur aus gewählten PIDs
        if (!empty($pids)) {
            $query->andWhere(
                $queryBuilder->expr()->in('pid', $queryBuilder->createNamedParameter($pids, Connection::PARAM_INT_ARRAY))
                );
        }
        
        // 3. Filter: Nur Departments der gewählten Area (falls eine gewählt wurde)
        if (!empty($selectedArea) && $selectedArea !== '0') {
            $query->andWhere(
                $queryBuilder->expr()->eq('area', $queryBuilder->createNamedParameter($selectedArea))
                );
        }
        
        $rows = $query->groupBy('department')
        ->orderBy('department', 'ASC')
        ->executeQuery()
        ->fetchAllAssociative();
        
        // Das Label aus der XLF laden
        $labelAllDepartments = $this->getLanguageService()->sL(
            'LLL:EXT:person/Resources/Private/Language/locallang_db.xlf:flexform.all_departments'
            );
        
        $params['items'][] = [$labelAllDepartments, '0'];
        foreach ($rows as $row) {
            $params['items'][] = [$row['department'], $row['department']];
        }
    }
    
    
    public function getUsersByDepartment(array &$params): void
    {
        $row = $params['row'];
        
        $pages = $this->ensureString($row['settings.pages'] ?? '');
        $selectedArea = $this->ensureString($row['settings.area'] ?? '');
        $selectedDept = $this->ensureString($row['settings.department'] ?? '');
        
        $pids = GeneralUtility::intExplode(',', $pages, true);
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        
        $query = $queryBuilder
            ->select('uid', 'username', 'last_name', 'first_name')
            ->from('fe_users');
        
        // Filter PIDs
        if (!empty($pids)) {
            $query->andWhere($queryBuilder->expr()->in('pid', $queryBuilder->createNamedParameter($pids, Connection::PARAM_INT_ARRAY)));
        }
        
        // Filter Area
        if (!empty($selectedArea) && $selectedArea !== '0') {
            $query->andWhere($queryBuilder->expr()->eq('area', $queryBuilder->createNamedParameter($selectedArea)));
        }
        
        // Filter Department
        if (!empty($selectedDept) && $selectedDept !== '0') {
            $query->andWhere($queryBuilder->expr()->eq('department', $queryBuilder->createNamedParameter($selectedDept)));
        }
        
        $rows = $query->orderBy('last_name', 'ASC')->executeQuery()->fetchAllAssociative();
        
        // Das Label aus der XLF laden
        $labelAllUser = $this->getLanguageService()->sL(
            'LLL:EXT:person/Resources/Private/Language/locallang_db.xlf:flexform.all_user'
            );
        
        // $params['items'][] = [$labelAllUser, '0']; Für Multiselection ausblenden
        
        foreach ($rows as $row) {
            $label = ($row['last_name'] || $row['first_name'])
            ? $row['last_name'] . ', ' . $row['first_name']
            : $row['username'];
            
            $params['items'][] = [$label, (string)$row['uid']];
        }
    }
}
