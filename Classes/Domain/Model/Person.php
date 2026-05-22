<?php

declare(strict_types=1);

namespace T3ac\Person\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Person extends AbstractEntity
{
    protected string $department = '';
    protected string $office = '';
    protected string $area = '';
    protected string $position = '';
    protected string $name = '';
    protected string $firstName = '';
    protected string $lastName = '';
    protected string $email = '';
    protected string $city = '';
    protected int $zip = 0;
    protected string $telephone = '';
    protected string $fax = '';
    protected string $address = '';
    protected int $gender = 0;
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getLastName(): string
    {
        return $this->lastName;
    }
    
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getCity(): string
    {
        return $this->city;
    }
    
    public function getZip(): int
    {
        return $this->zip;
    }
    
    public function getAddress(): string
    {
        return $this->address;
    }
    
    public function getFax(): string
    {
        return $this->fax;
    }
    
    public function getTelephone(): string
    {
        return $this->telephone;
    }
    
    public function getDepartment(): string
    {
        return $this->department;
    }
    
    public function setDepartment(string $department): void
    {
        $this->department = $department;
    }
    
    public function getOffice(): string
    {
        return $this->office;
    }
    
    public function setOffice(string $office): void
    {
        $this->office = $office;
    }
    
    public function getPosition(): string
    {
        return $this->position;
    }
    
    public function setPosition(string $position): void
    {
        $this->position = $position; // Korrigiert (war vorher $this->area)
    }
    
    public function getArea(): string
    {
        return $this->area;
    }
    
    public function setArea(string $area): void
    {
        $this->area = $area;
    }
    
    public function getGender(): int
    {
        return $this->gender;
    }
    
    public function setGender(int $gender): void
    {
        $this->gender = $gender;
    }
}
