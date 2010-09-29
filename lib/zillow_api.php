<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * PHP Zillow API library
 */
class ZillowApi
{
   /**
    * Request timeout
    * @var integer
    */
   public static $request_timeout = 2;
   
   /**
    * Proxy address (if needed)
    * @var string
    */
   public static $proxy_address = null;
   
   /**
    * Proxy port (if needed)
    * @var integer
    */
   public static $proxy_port = null;
   
   /**
    * Proxy username (if needed)
    * @var string
    */
   public static $proxy_user = null;
   
   /**
    * Proxy password (if needed)
    * @var string
    */
   public static $proxy_pass = null;
   
   /**
    * Max number of socket reads
    * @var integer
    */
   public static $read_retries = 3;
   
   /**
    * API base URL
    * @var string
    */
   public static $url = 'http://www.zillow.com/webservice/';
   
   /**
    * API key
    * @var string
    */
   public static $key = '';
   
   
   // calls to implement:
   // http://www.zillow.com/howto/api/APIOverview.htm
   // * GetZestimate API
   // * GetChart API
   // * GetComps API
   // * GetDeepSearchResults API
   // * GetDeepComps API
   // * GetUpdatedPropertyDetails API


   /**
    * Gets data from the GetSearchResults API
    *
    * Options available:
    *
    * <ul>
    *   <li><b>zws-id</b> <i>(string)</i>: Zillow API key (default: self::$key)</li>
    *   <li><b>address</b> <i>(string)</i>: Address (default: null)</li>
    *   <li><b>citystatezip</b> <i>(string)</i>: City, state and zipcode (default: null)</li>
    * </ul>
    * @see http://www.zillow.com/howto/api/GetSearchResults.htm
    * @param array $options finder options (must include citystatezip and address)
    * @param boolean $rax_xml return the raw XML response
    * @return ZillowSearchResults results data
    */
   public static function getSearchResults($options = array(), $raw_xml = false)
   {
      $default_options = array(
         'zws-id' => self::$key,
         'address' => null,
         'citystatezip' => null,
      );
      $options = array_merge($default_options, $options);
      
      // check requirements
      if ((is_null($options['citystatezip'])) || (is_null($options['address']))
      ) {
         throw new Exception('You must provide an address and citystatezip to getSearchResults');
      }
      
      return self::sendRequest('ZillowSearchResults', 'GetSearchResults', $options, $raw_xml);
   }
   
   /**
    * Gets data from the GetDemographics API
    *
    * Options available:
    *
    * <ul>
    *   <li><b>zws-id</b> <i>(string)</i>: Zillow API key (default: self::$key)</li>
    *   <li><b>regionid</b> <i>(integer)</i>: Zillow region ID (default: null)</li>
    *   <li><b>state</b> <i>(string)</i>: State (default: null)</li>
    *   <li><b>city</b> <i>(string)</i>: City (default: null)</li>
    *   <li><b>neighborhood</b> <i>(string)</i>: Neighborhood (default: null)</li>
    *   <li><b>zip</b> <i>(string)</i>: Zip code(default: null)</li>
    * </ul>
    * @see http://www.zillow.com/howto/api/GetDemographics.htm
    * @param array $options finder options (must include regionid, state/city, city/neighborhood or zip)
    * @param boolean $rax_xml return the raw XML response
    * @return ZillowDemographics demographics data
    */
   public static function getDemographics($options = array(), $raw_xml = false)
   {
      $default_options = array(
         'zws-id' => self::$key,
         'regionid' => null,
         'state' => null,
         'city' => null,
         'neighborhood' => null,
         'zip' => null,
      );
      $options = array_merge($default_options, $options);
      
      // check requirements
      if ((is_null($options['regionid'])) &&
          (is_null($options['state']) && is_null($options['city'])) && 
          (is_null($options['city']) && is_null($options['neighborhood'])) &&
          (is_null($options['zip']))
      ) {
         throw new Exception('You must provide a regionid, state/city, city/neighborhood or zip to getDemographics');
      }
      
      return self::sendRequest('ZillowDemographics', 'GetDemographics', $options, $raw_xml);
   }
   
   /**
    * Gets data from the GetRegionChart API
    *
    * Options available:
    *
    * <ul>
    *   <li><b>zws-id</b> <i>(string)</i>: Zillow API key (default: self::$key)</li>
    *   <li><b>state</b> <i>(string)</i>: State (default: null)</li>
    *   <li><b>city</b> <i>(string)</i>: City (default: null)</li>
    *   <li><b>neighborhood</b> <i>(string)</i>: Neighborhood (default: null)</li>
    *   <li><b>zip</b> <i>(string)</i>: Zip code(default: null)</li>
    *   <li><b>unit-type</b> <i>(string)</i>: Unit type (percent, dollar) (default: dollar)</li>
    *   <li><b>width</b> <i>(integer)</i>: Graph width (between 200 and 600) (default: 300)</li>
    *   <li><b>height</b> <i>(integer)</i>: Graph height (between 100 and 300) (default: 150)</li>
    *   <li><b>chartDuration</b> <i>(string)</i>: Duration (1year, 5years, 10years) (default: 1year)</li>
    * </ul>
    * @see http://www.zillow.com/howto/api/GetRegionChart.htm
    * @param array $options finder options
    * @param boolean $rax_xml return the raw XML response
    * @return ZillowRegionChart chart data
    */
   public static function getRegionChart($options = array(), $raw_xml = false)
   {
      $default_options = array(
         'zws-id' => self::$key,
         'state' => null,
         'city' => null,
         'neighborhood' => null,
         'zip' => null,
         'unit-type' => 'dollar',
         'width' => 300,
         'height' => 150,
         'chartDuration' => '1year',
      );
      $options = array_merge($default_options, $options);
      
      return self::sendRequest('ZillowRegionChart', 'GetRegionChart', $options, $raw_xml);
   }
   
   /**
    * Gets data from the GetRegionChildren API
    *
    * Options available:
    *
    * <ul>
    *   <li><b>zws-id</b> <i>(string)</i>: Zillow API key (default: self::$key)</li>
    *   <li><b>rid</b> <i>(integer)</i>: Zillow region ID (default: null)</li>
    *   <li><b>country</b> <i>(string)</i>: Country (default: null)</li>
    *   <li><b>state</b> <i>(string)</i>: State (default: null)</li>
    *   <li><b>county</b> <i>(string)</i>: County (default: null)</li>
    *   <li><b>city</b> <i>(string)</i>: City (default: null)</li>
    *   <li><b>childtype</b> <i>(string)</i>: Child search type (default: null)</li>
    * </ul>
    * @see http://www.zillow.com/howto/api/GetRegionChildren.htm
    * @param array $options finder options (must include rid, country or state)
    * @param boolean $rax_xml return the raw XML response
    * @return ZillowRegionChildren regions data
    */
   public static function getRegionChildren($options = array(), $raw_xml = false)
   {
      $default_options = array(
         'zws-id' => self::$key,
         'rid' => null,
         'country' => null,
         'state' => null,
         'county' => null,
         'city' => null,
         'childtype' => 'zipcode',
      );
      $options = array_merge($default_options, $options);
      
      // check requirements
      if (is_null($options['rid']) && is_null($options['state']) && is_null($options['county'])) {
         throw new Exception('You must provide a rid, state or country to getRegionChildren');
      }
      
      return self::sendRequest('ZillowRegionChildren', 'GetRegionChildren', $options, $raw_xml);
   }
   
   /**
    * Gets data from the GetRateSummary API
    *
    * Options available:
    *
    * <ul>
    *   <li><b>zws-id</b> <i>(string)</i>: Zillow API key (default: self::$key)</li>
    *   <li><b>state</b> <i>(string)</i>: State (default: null)</li>
    * </ul>
    * @see http://www.zillow.com/howto/api/GetRateSummary.htm
    * @param array $options finder options (must include state)
    * @param boolean $rax_xml return the raw XML response
    * @return ZillowRateSummary rate summary data
    */
   public static function getRateSummary($options = array(), $raw_xml = false)
   {
      $default_options = array(
         'zws-id' => self::$key,
         'state' => null,
      );
      $options = array_merge($default_options, $options);
      
      if (is_null($options['state'])) {
         throw new Exception('You must provide a rid, state or country to getRegionChildren');
      }
      
      return self::sendRequest('ZillowRateSummary', 'GetRateSummary', $options, $raw_xml);
   }
   
   /**
    * Gets data from the GetMonthlyPayments API
    *
    * Options available:
    *
    * <ul>
    *   <li><b>zws-id</b> <i>(string)</i>: Zillow API key (default: self::$key)</li>
    *   <li><b>price</b> <i>(float)</i>: Property price (default: null)</li>
    *   <li><b>down</b> <i>(float)</i>: Percent down payment (default: null)</li>
    *   <li><b>dollarsdown</b> <i>(float)</i>: Dollars down payment (default: null)</li>
    *   <li><b>zip</b> <i>(float)</i>: Zip code (for tax estimates) (default: null)</li>
    * </ul>
    * @see http://www.zillow.com/howto/api/GetMonthlyPayments.htm
    * @param array $options finder options (must include price)
    * @param boolean $rax_xml return the raw XML response
    * @return ZillowMonthlyPayments payment data
    */
   public static function getMonthlyPayments($options = array(), $raw_xml = false)
   {
      $default_options = array(
         'zws-id' => self::$key,
         'price' => null,
         'down' => null,
         'dollarsdown' => null,
         'zip' => null,
      );
      $options = array_merge($default_options, $options);
      
      if (is_null($options['price'])) {
         throw new Exception('You must provide a price to getMonthlyPayments');
      }
      
      return self::sendRequest('ZillowMonthlyPayments', 'GetMonthlyPayments', $options, $raw_xml);
   }
   
   /**
    * Turns an array of key/value pairs into URL parameters
    * @param array $option options
    * @return string url parameters
    */
   protected static function optionsToParameters($options)
   {
      $rc = array();
      foreach ($options as $key => $value) {
         if (!is_null($value)) {
            $rc[] = $key . '=' . urlencode($value);
         }
      }
      
      return implode('&', $rc);
   }
   
   /**
    * Send API request
    * @param string $klass return type
    * @param string $method API method
    * @param array $options request parameters
    * @param boolean $rax_xml return the raw XML response
    * @return mixed parsed return (type is $klass)
    */
   protected static function sendRequest($klass, $method, $options = array(), $raw_xml = false)
   {
      $rc = new $klass();
      try {
         $xml = self::callWebService(self::$url . $method . '.htm?' . self::optionsToParameters($options));
      } catch (Exception $e) {
         $rc->error_code = $e->getCode();
         $rc->error_message = $e->getMessage();
         return $raw_xml ? null : $rc;
      }
      
      if ($raw_xml) {
         return $xml;
      }
      
      $rc->parseXml($xml);
      
      return $rc;
   }
   
   /**
    * Makes HTTP request to geocoder service
    * @param string $url URL to request
    * @return string service response
    * @throws Exception if cURL library is not installed
    * @throws Exception on cURL error
    */
   protected static function callWebService($url)
   {
      if (!function_exists('curl_init')) {
         throw new Exception('The cURL library is not installed.');
      }
      
      $url_info = parse_url($url);
      
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::$request_timeout);
      curl_setopt($curl, CURLOPT_TIMEOUT, self::$request_timeout);
      
      // check for proxy
      if (!is_null(self::$proxy_address)) {
         curl_setopt($curl, CURLOPT_PROXY, self::$proxy_address . ':' . self::$proxy_port);
         curl_setopt($curl, CURLOPT_PROXYUSERPWD, self::$proxy_user . ':' . self::$proxy_pass);
      }

      // check for http auth:
      if (isset($url_info['user'])) {
         $user_name = $url_info['user'];
         $password = isset($url_info['pass']) ? $url_info['pass'] : '';
         
         curl_setopt($curl, CURLOPT_USERPWD, $user_name . ':' . $password);
      }
      
      $error = 'error';
      $retries = 0;
      while (trim($error) != '' && $retries < self::$read_retries) {
         $rc = curl_exec($curl);
         $error = curl_error($curl);
         $retries++;
      }
      curl_close($curl);
      
      if (trim($error) != '') {
         throw new Exception($error);
      }
      
      return $rc;
   }
}
