<?php

namespace App\Libraries;

class RestCountriesHelper {
    public static function getCountriesFromString($string) {
      // This is the limit defined in the challenge
      $resultLimit = 50;

      // limit to full name, alpha code 2, alpha code 3,
      // flag image, region, subregion, population, list of languages

      $url = "https://restcountries.eu/rest/v2/name/" . $string . "?fields=name;alpha2Code;alpha3Code;flag;region;subregion;population;languages;";
      $client = new \GuzzleHttp\Client();

      try {
        $res = $client->request('GET', $url);
      } catch(\GuzzleHttp\Exception\ClientException $ce) {
        // Rest Countries responds with 404 if no results
        $statusCode = $ce->getResponse()->getStatusCode();
        if ($statusCode == '404') {
          $returnArray = [
            'countries' => [],
            'regions' => [],
            'subregions' => [],
            'countryCount' => []
          ];

          $apiResponse = json_encode($returnArray);
          return $apiResponse;
        }
      }

      $jsonResult = $res->getBody();

      $decodedResult = json_decode($jsonResult, true);

      // Sort countries by name and population
      $sortedArray = array_multisort(array_column($decodedResult, 'name'), SORT_NATURAL,array_column($decodedResult, 'population'), SORT_DESC, $decodedResult);

      $countries = $decodedResult;

      $regions = array();
      $subRegions = array();

      $countriesToReturn = array();
      $countryCounter = 0;

      // Make regions and subregions counters
      foreach($countries as $countryArray) {
        $region = $countryArray['region'];
        if(array_key_exists($region,$regions) ){
          $regions[$region] = $regions[$region] + 1;
        } else {
          $regions[$countryArray['region']] = 1;
        }

        $subRegion = $countryArray['subregion'];
        if(array_key_exists($subRegion,$subRegions) ){

          $subRegions[$subRegion] = $subRegions[$subRegion] + 1;
        } else {
          $subRegions[$countryArray['subregion']] = 1;
        }
        $countryCounter++;
        if($countryCounter == $resultLimit) {
          break;
        }
      }
      $regionsArray = array();
      foreach($regions as $regionName => $count) {
        $regionsArray[] = [
          'name' => $regionName,
          'count' => $count
        ];
      }

      $subRegionsArray = array();
      foreach($subRegions as $subRegionName => $count) {
        $subRegionsArray[] = [
          'name' => $subRegionName,
          'count' => $count
        ];
      }

      $returnArray = [
        'countries' => $decodedResult,
        'regions' => $regionsArray,
        'subregions' => $subRegionsArray,
        'countryCount' => $countryCounter
      ];

      $apiResponse = json_encode($returnArray);
      return $apiResponse;
    }

}
