<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Country Search</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
                padding-left: 10px;
                padding-right: 10px;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .displayResultsTable {
              border: 1px solid black;
              margin-right:auto;
              margin-left:auto;
              width: 90%;
              display: block;
              overflow-x: auto;
              white-space: nowrap;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Country Search
                </div>

                <div class="SearchForm m-b-md">
                  <form id="CountrySearchForm">
                    Country Name:<br>
                    <input id="countryField" type="text" name="countryName"><br>
                    <input type="submit">
                  </form>
                </div>

                <div class="CountrySearchResultList m-b-md">
                  Countries:<br>
                  <table id="CountryTable" class="displayResultsTable">
                  </table>
                  Countries Count: <span id="countryCount"></span><br><br>
                  Regions:<br>
                  <table id="RegionTable" class="displayResultsTable">
                  </table>
                  Sub Regions:<br>
                  <table id="SubRegionTable" class="displayResultsTable">
                  </table>
                </div>
            </div>
        </div>
    </body>
    <script id="table-row-hidden-template" type="text/x-custom-template">
      <tr>
        <td class="countryName">Name</th>
        <td class="alphaCode2">Alpha code 2</th>
        <td class="alphaCode3">Alpha code 3</th>
        <td><img width="70%" class= "flagImage" src=""></th>
        <td class="region">Region</th>
        <td class="region">Subregion</th>
        <td class="population">Population</th>
        <td class="languages">Languages</th>
      <tr>
    </script>
    <script id="region-table-row-hidden-template" type="text/x-custom-template">
    <tr>
      <td class="region">Name</th>
      <td class="regionCount">0</th>
    <tr>
    </script>
    <script id="subregion-table-row-hidden-template" type="text/x-custom-template">
      <tr>
        <td class="subRegion">Name</th>
        <td class="subRegionCount">0</th>
      <tr>
    </script>
    <script id="table-header-hidden-template" type="text/x-custom-template">
      <tr>
        <td class="countryName">Name</th>
        <td class="alphaCode2">Alpha code 2</th>
        <td class="alphaCode3">Alpha code 3</th>
        <td class="flagImage">Flag Image</th>
        <td class="region">Region</th>
        <td class="subRegion">Subregion</th>
        <td class="population">Population</th>
        <td class="languages">Languages</th>
      <tr>
    </script>
    <script>
      $("#CountrySearchForm").submit(function(e) {
        $("#CountryTable").empty();
        $("#RegionTable").empty();
        $("#SubRegionTable").empty();
        // clear Countries Table

      var countryFieldValue = $('#countryField').val();
      if (countryFieldValue == "") {
        e.preventDefault();
        alert("You need to input at least one character.");
        return  false;
      }


      var url = "/api/countrySearch/" + countryFieldValue; // the script where you handle the form input.

      $.ajax({
             type: "GET",
             url: url,
             success: function(data)
             {
                 var parsedResult = JSON.parse(data);
                 console.log(parsedResult); // show response from the php script.
                 if (parsedResult.countries.length === 0) {
                   e.preventDefault();
                   alert("There were no results for your search.");
                   return  false;
                 }
                 var headerTemplate = $('#table-header-hidden-template').html();
                 var item = $(headerTemplate).clone();
                 $('#CountryTable').append(item);
                 var rowTemplate = $('#table-row-hidden-template').html();
                 var regionRowTemplate = $('#region-table-row-hidden-template').html();
                 var subRegionRowTemplate = $('#subregion-table-row-hidden-template').html();
                 parsedResult.countries.forEach(function(country) {
                   var languagesString = "";
                   country.languages.forEach(function(language) {
                     var eachLanguage = language.name + ',';
                     languagesString += eachLanguage;
                   });
                   var countryTableRow = $(rowTemplate).clone();
                   $(countryTableRow).find('.countryName').html(country.name);
                   $(countryTableRow).find('.alphaCode2').html(country.alpha2Code);
                   $(countryTableRow).find('.alphaCode3').html(country.alpha3Code);
                   $(countryTableRow).find('.flagImage').attr('src', country.flag);
                   $(countryTableRow).find('.region').html(country.region);
                   $(countryTableRow).find('.subRegion').html(country.subregion);
                   $(countryTableRow).find('.population').html(country.population);
                   $(countryTableRow).find('.languages').html(languagesString);

                   $('#CountryTable').append(countryTableRow);
                 });
                 parsedResult.regions.forEach(function(region) {
                   var regionTableRow = $(regionRowTemplate).clone();
                   $(regionTableRow).find('.region').html(region.name);
                   $(regionTableRow).find('.regionCount').html(region.count);
                   $('#RegionTable').append(regionTableRow);
                 });
                 parsedResult.subregions.forEach(function(region) {
                   var subRegionTableRow = $(subRegionRowTemplate).clone();
                   $(subRegionTableRow).find('.subRegion').html(region.name);
                   $(subRegionTableRow).find('.subRegionCount').html(region.count);
                   $('#SubRegionTable').append(subRegionTableRow);
                 });
                 $("#countryCount").html(parsedResult.countryCount);
             }
           });

        e.preventDefault(); // avoid to execute the actual submit of the form.
      });

    </script>
</html>
