# WeatherData Container

Can be used to fetch weather data for specific `GeoLocations` which consists of latitude, longitude and a timestamp.

## Unit conversion
Unit conversion currently is a work in progress. Conversion will happen on the fly if you specify the `conversion=x` parameter in your query.
If those are specified we will pass the fetched data from our main task to the converter task which will try to convert all data that match your request.

#### Syntax
- `?convert=<string>`
    - `<string>`: multiple conversions separated by `;` e.g. `?convert=*:C;*L`  converts all applicable values to °C and Liters.
    - You can specify either all data points applicable by using `*` as a wildcard symbol or specify the parameters like this: `?convert=a,b,c:C`to convert all parameters whose names contain a, b or c into °C if possible.
    - Failed conversions are currently not 
- Each weather data point will only be converted once! So if you specify `?convert=*:C,*:K` all values will be converted to °C
  
#### Supported Units
Currently with the two sample gateways it is possible to gather data in the following units:

| unit | supported |
|------|----------|
|`(0 - 1)`| |
|`%`| |
|`% (n/8 where -1 means error)`| |
|`~`| |
|`bool (0 no precipitation, 1 precipitation)`| |
|`C`| &#10004;|
|`deg`| |
|`hPA`| &#10004; |
|`integer (0-9)`| |
|`K`|&#10004; |
|`kg m**-2`|&#10004; |
|`kg s**2 m**-5`| |
|`m s**-1`| |
|`m**2 s**-2`| |
|`min`|&#10004; |
|`mm`|&#10004; |
|`Pa`|&#10004; |

Currently the the [Convertor Library](https://github.com/olifolkerd/convertor) by oli folkerd was [forked](https://github.com/FWidm/convertor/blob/master/src/Convertor.php) and adapted to the needs of this api. 



## Gateways
Gateways are objects that are able to retrieve data from weather sources. Additionally they also transform the retrieved format into a common format:

### Adding another Gateway
- To add another `*GateWay` your new Class has to extend `AbstractDataRetrievalGateway` and implement the following methods:
    - `retrieve()`: Internal function that describes how you fetch data. Either by calling another Service (i.e. `CopernicusRetrievalGateway`) or using a Library (i.e. `DWDRetrievalGateway`)
    - `parseToWeatherData()`: Parse the retrieved data to the expected format by calling the corresponding Transformer.
    - Optional: implement `parseToWeatherSource()`: if your service provides additional data that can be relevant for severl entries. The German Meteorological Service (DWD) uses measuring stations and provides informations about the stations when retrieving data. This information about the station is in return saved into a `WeatherSource` model.
- When everything is finished yor last step is to edit the `DataRetrievalJob`:
    - Register the new implemented gateway in the settings in `weather.php` like this:
      ```
      'gateways' => [
                        new DWDRetrievalGateway(),
                        new CopernicusRetrievalGateway(),
                        new YourRetrievalGateway()
                    ];
      ```


#### Example `*ToWeatherDataTransformer` output:

```
Array
    (
    [data] => Array
    (
        [source_id] =>
        [source] => Copernicus
        [value] => 0.83590698242188
        [description] => stdClass Object
        (
            [dataTime] => 0
            [name] => High cloud cover
            [step] => 0
            [date] => 20170928
            [shortName] => hcc
            [paramId] => 188
            [index] => 45047
        )

        [classification] => Cloudiness
        [distance] => 13.558712398993
        [lat] => 48.29265233053
        [lon] => 9.5
        [date] => Carbon\Carbon Object
        (
            [date] => 2017-09-28 00:00:00.000000
            [timezone_type] => 1
            [timezone] => +00:00
        )
        [type] => High cloud cover
        [geo_location_id] => 6
        [unit] => (0 - 1)
        )
    )
```

#### Example `* ToWeatherSourceTransformer` output
Note that data is a `json`-column in your db and can contain a multitude of data.

```
Array
(
    [data] => Array
    (
        [original_id] => 03402
        [data] => FWidm\DWDHourlyCrawler\Model\DWDStation Object
            (
                [id:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => 03402
                [from:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => Carbon\Carbon Object
                    (
                        [date] => 2002-11-01 10:14:27.000000
                        [timezone_type] => 3
                        [timezone] => UTC
                    )

                [until:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => Carbon\Carbon Object
                    (
                        [date] => 2017-10-07 10:14:27.000000
                        [timezone_type] => 3
                        [timezone] => UTC
                    )

                [height:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => 750
                [latitude:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => 48.3851
                [longitude:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => 9.4837
                [name:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => Münsingen-Apfelstetten
                [state:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => Baden-Württemberg
                [active:FWidm\DWDHourlyCrawler\Model\DWDStation:private] => 1
            )

        [source] => DWD
    )
)
```
