<?php

namespace App\Http\Controllers\CountrySearch;

use App\Http\Controllers\Controller;
use App\Libraries\RestCountriesHelper;

class CountrySearchController extends Controller
{
  public function search($string)
  {
      $jsonResult = RestCountriesHelper::getCountriesFromString($string);
      return $jsonResult;
  }
}
