<?php

declare(strict_types=1);

namespace T3ac\Person\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "Personen Plugin" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Georg Ringer
 */
/**
 * Person
 */
class SearchFormDto extends AbstractEntity
{

    /** @var string */
    protected $searchWord = '';

    /** @var string */
    protected $firstChar = '';

    /**
     * @return string
     */
    public function getSearchWord(): string
    {
        return $this->searchWord;
    }

    /**
     * @param string $searchWord
     * @return SearchFormDto
     */
    public function setSearchWord(string $searchWord): SearchFormDto
    {
        $this->searchWord = $searchWord;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstChar(): string
    {
        return $this->firstChar;
    }

    /**
     * @param string $firstChar
     * @return SearchFormDto
     */
    public function setFirstChar(string $firstChar): SearchFormDto
    {
        $this->firstChar = $firstChar;
        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->searchWord) && empty($this->firstChar);
    }

}
