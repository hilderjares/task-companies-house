<?php

namespace App;

class CompanyValidation
{
    public function officerNameIsValid(string $officerName, object $responseOfficer): bool
    {
        $officerJson = json_decode((string)$responseOfficer->getBody());
        $officers = $officerJson->items;

        foreach ($officers as $officer) {
            if ($officer->name == $officerName) {
                return true;
            }
        }

        return false;
    }
}