<?php

namespace Sarotnem\RyanairApiClient;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class RyanairApiClient
{
    /**
     * @var Guzzle API Client
     */
    protected $client;

    /**
     * Create new instance of GuzzleHttp
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('ryanair.api_base') . '/'
        ]);
    }

    /**
     * [makeRequest The base function of all requests]
     * @param  $endpoint The endpoint url
     * @param  array  $query    The query string for the request
     * @return mixed
     */
    private function makeRequest($endpoint, $query = [])
    {
        $query['apikey'] = config('ryanair.api_key');

        $apiRequest = $this->client->request('GET', $endpoint, [
            'query' => $query,
            ['debug' => 'true'],
        ]);

        $response = $apiRequest->getBody()->getContents();
        $apiData= json_decode($response);

        return $apiData;
    }

    /**
     * [getRoutes Returns list of all active routes]
     * @return mixed
     */
    public function getRoutes()
    {
        return $this->makeRequest("core/3/routes");
    }

    /**
     * [getRoutesIataCodesFromAirport
     * Returns list of three-letter IATA codes for the arrival airports
     * on all active routes from the airport given by its three-letter IATA code]
     * @param  string $airportFrom Origin Airport IATA Code
     * @return mixed
     */
    public function getRoutesIataCodesFromAirport($airportFrom)
    {
        return $this->makeRequest("core/3/routes/{$airportFrom}/iataCodes");
    }

    /**
     * [getRoutesFromAirport
     * Returns list of all active routes from the airport given by its three-letter IATA code]
     * @param  string $airportFrom Origin Airport IATA Code
     * @return mixed
     */
    public function getRoutesFromAirport($airportFrom)
    {
        return $this->makeRequest("core/3/routes/{$airportFrom}");
    }

    /**
     * [getRegions Returns list of regions in countries]
     * @return mixed
     */
    public function getRegions()
    {
        return $this->makeRequest("core/3/regions");
    }

    /**
     * [getCurrencies Returns list of all available currencies]
     * @param  string $market Market Code
     * @return mixed
     */
    public function getCurrencies($market = '')
    {
        return $this->makeRequest("core/3/currencies", [
            "market" => $market
        ]);
    }

    /**
     * [getCountries Returns list of all available countries]
     * @return mixed
     */
    public function getCountries()
    {
        return $this->makeRequest("core/3/countries");
    }

    /**
     * [getCountries Returns list of all available cities]
     * @return mixed
     */
    public function getCities()
    {
        return $this->makeRequest("core/3/cities");
    }

    /**
     * [getAirports Returns list of all active airports]
     * @return mixed
     */
    public function getAirports()
    {
        return $this->makeRequest("core/3/airports");
    }

    /**
     * [getDiscounts Returns available discounts]
     * @return mixed
     */
    public function getDiscounts()
    {
        return $this->makeRequest("discount/3/discounts");
    }

    /**
     * [getSimilar Returns list (ascending) of similar fares for given fare parameters]
     * @param  string $departureAirportIataCode  Three letter departure airport IATA code
     * @param  string $arrivalAirportIataCode    Three letter arrival airport IATA code
     * @param  string $outboundDepartureDateFrom Date in format: yyyy-MM-dd
     * @param  string $outboundDepartureDateTo   Date in format: yyyy-MM-dd
     * @param  array  $parameters                Optional parameters to be passed
     * (See: http://developer.ryanair.com/farefinder-api/apis/get/3/oneWayFares/similar)
     * @return mixed
     */
    public function getSimilar($departureAirportIataCode, $arrivalAirportIataCode, $outboundDepartureDateFrom, $outboundDepartureDateTo, $parameters = [])
    {
        $query = [
            "departureAirportIataCode" => $departureAirportIataCode,
            "arrivalAirportIataCode" => $arrivalAirportIataCode,
            "outboundDepartureDateFrom" => $outboundDepartureDateFrom,
            "outboundDepartureDateTo" => $outboundDepartureDateTo,
        ];

        $query = array_merge($parameters, $query);

        return $this->makeRequest("farefinder/3/oneWayFares/similar", $query);
    }

    /**
     * [getCheapest Returns sorted list (ascending) of one way fares for given filter parameters]
     * @param  string $departureAirportIataCode  Three letter departure airport IATA code
     * @param  string $outboundDepartureDateTo   Date in format: yyyy-MM-dd
     * @param  string $outboundDepartureDateFrom Date in format: yyyy-MM-dd
     * @param  array  $parameters                Optional parameters to be passed
     * (See: http://developer.ryanair.com/farefinder-api/apis/get/3/oneWayFares)
     * @return mixed
     */
    public function getCheapestOneWay($departureAirportIataCode, $outboundDepartureDateFrom, $outboundDepartureDateTo, $parameters = [])
    {
        $query = [
            "departureAirportIataCode" => $departureAirportIataCode,
            "outboundDepartureDateFrom" => $outboundDepartureDateFrom,
            "outboundDepartureDateTo" => $outboundDepartureDateTo,
        ];

        $query = array_merge($parameters, $query);

        return $this->makeRequest("farefinder/3/oneWayFares", $query);
    }

    /**
     * [getCheapestPerDay
     * Returns sorted list (ascending) of one way fares for given filter parameters per day.
     * Set airport URL params and one of date parameter to build up a correct request.]
     * @param  string $departureAirportIataCode Three letter departure airport IATA code
     * @param  string $arrivalAirportIataCode   Three letter arrival airport IATA code
     * @param  array  $parameters               Pass at least one date parameter
     * (See: http://developer.ryanair.com/farefinder-api/apis/get/3/oneWayFares/%7BdepartureAirportIataCode%7D/%7BarrivalAirportIataCode%7D/cheapestPerDay)
     * @return mixed
     */
    public function getCheapestPerDayOneWay($departureAirportIataCode, $arrivalAirportIataCode, $parameters = [])
    {
        return $this->makeRequest("farefinder/3/oneWayFares/{$departureAirportIataCode}/{$arrivalAirportIataCode}/cheapestPerDay", $parameters);
    }

    /**
     * [getCheapestRoundTrip
     * Returns sorted list (ascending) of round trip fares for given filter parameters.
     * Set all required parameters and one arrival code (airport or country or region etc.) to build up correct request.]
     * @param  [type] $departureAirportIataCode  Three letter departure airport IATA code
     * @param  [type] $outboundDepartureDateFrom Date in format: yyyy-MM-dd
     * @param  [type] $outboundDepartureDateTo   Date in format: yyyy-MM-dd
     * @param  [type] $inboundDepartureDateFrom  Date in format: yyyy-MM-dd
     * @param  [type] $inboundDepartureDateTo    Date in format: yyyy-MM-dd
     * @param  array  $parameters                Optional parameters to be passed
     * (See: http://developer.ryanair.com/farefinder-api/apis/get/3/roundTripFares)
     * @return mixed
     */
    public function getCheapestRoundTrip($departureAirportIataCode, $outboundDepartureDateFrom, $outboundDepartureDateTo, $inboundDepartureDateFrom, $inboundDepartureDateTo, $parameters = [])
    {
        $query = [
            "departureAirportIataCode" => $departureAirportIataCode,
            "outboundDepartureDateFrom" => $outboundDepartureDateFrom,
            "outboundDepartureDateTo" => $outboundDepartureDateTo,
            "inboundDepartureDateFrom" => $inboundDepartureDateFrom,
            "inboundDepartureDateTo" => $inboundDepartureDateTo,
        ];

        $query = array_merge($parameters, $query);

        return $this->makeRequest("farefinder/3/roundTripFares", $query);
    }

    /**
     * [getCheapestPerDayRoundTrip
     * Returns sorted list (ascending) of round trip fares for given filter parameters per day.
     * Set ariports URL params and one of date parameter to build up a correct request.]
     * @param  [type] $departureAirportIataCode Three letter departure airport IATA code
     * @param  [type] $arrivalAirportIataCode   Three letter departure airport IATA code
     * @param  array  $parameters               Pass at least two date parameters (Outbound & Inbound)
     * (See: http://developer.ryanair.com/farefinder-api/apis/get/3/roundTripFares/%7BdepartureAirportIataCode%7D/%7BarrivalAirportIataCode%7D/cheapestPerDay)
     * @return mixed
     */
    public function getCheapestPerDayRoundTrip($departureAirportIataCode, $arrivalAirportIataCode, $parameters = [])
    {
        return $this->makeRequest("farefinder/3/roundTripFares/{$departureAirportIataCode}/{$arrivalAirportIataCode}/cheapestPerDay", $parameters);
    }
}
