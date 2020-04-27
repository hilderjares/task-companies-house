<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CompanyController
{
    private JsonResponse $response;
    private Request $request;
    private Client $client;
    private CompanyValidation $validation;

    private const URL = "https://api.companieshouse.gov.uk";
    private const HEADERS = [
        'Authorization' => '9aV5UBNrxq9_pdq0R0OXCBzhIgzBs3502aAY5Ku6',
        'Accept' => 'application/json',
    ];

    function __construct(JsonResponse $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
        $this->client = new Client(['base_uri' => self::URL, 'hea']);
        $this->validation = new CompanyValidation();
    }

    public function search(): string
    {
        $companyName = $this->request->get('companyName');
        $officerName = $this->request->get('officerName');

        $responseCompany = $this->client->request('GET', '/search/companies', [
            'headers' => self::HEADERS,
            'query' => [
                'q' => $companyName,
            ],
        ]);

        $jsonCompany = json_decode((string)$responseCompany->getBody());
        $companies = $jsonCompany->items;
        $company = $this->getCompanyByName($companies, $companyName);

        if (!$company) {
            return json_encode([
                'error' => true,
                'message' => sprintf('A record for "%s" could not be found at Companies House', $companyName),
            ]);
        }

        $companyNumber = $company->company_number;
        $uriOfficerRequest = sprintf("/company/%d/officers", $companyNumber);
        $responseOfficer = $this->client->request('GET', $uriOfficerRequest, ['headers' => self::HEADERS]);

        if (!$this->validation->officerNameIsValid($officerName, $responseOfficer)) {
            return json_encode([
                'error' => true,
                'message' => sprintf(' "%s" could not be found as an officer of "%s" ', $officerName, $companyName),
            ]);
        }

        return json_encode(['error' => false, 'message' => $company->address_snippet]);
    }

    protected function getCompanyByName(array $companies, string $companyName): ?object
    {
        foreach ($companies as $company) {
            if ($company->title == $companyName) {
                return $company;
            }
        }

        return null;
    }
}