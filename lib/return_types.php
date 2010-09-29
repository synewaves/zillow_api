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
 * Base API return type
 */
class ZillowData
{
   /**
    * Error message
    * @var string
    */
   public $error_message = null;
   
   /**
    * Response error code
    * @var integer
    */
   public $error_code = null;
   
   /**
    * Are we nearing the warning limit?
    * @var boolean
    */
   public $warning_limit = false;
   
   
   /**
    * Was the response a success?
    * @return boolean success
    */
   public function success()
   {
      return $this->error_code == 0;
   }
   
   /**
    * Parses the status fields from xml
    * @param SimpleXmlElement $doc xml doc
    */
   protected function parseXmlStatus($doc)
   {
      $this->error_code = (int) $doc->message->code;
      $this->error_message = (string) $doc->message->text;
      
      if (isset($doc->message->{'limit-warning'})) {
         $this->warning_limit = true;
      }
   }
}


/**
 * Search Results data
 */
class ZillowSearchResults extends ZillowData
{
   /**
    * Results
    * @car array
    */
   public $results = array();
   
   
   /**
    * Parses the XML from Zillow 
    * @param string $xml xml response
    */
   public function parseXml($xml)
   {
      $doc = new SimpleXmlElement($xml);
      
      $this->parseXmlStatus($doc);
      
      if (!$this->success()) {
         return;
      }
      
      // get all of the results
      foreach ($doc->response->results->result as $result) {
         $this->results[] = new ZillowSearchResult($result);
      }
   }
}


/**
 * Single search result data
 */
class ZillowSearchResult
{
   /**
    * Zillow property ID
    * @var string
    */
   public $zpid = null;
   
   /**
    * Links to Zillow data pages
    * @var array
    */
   public $links = array();
   
   /**
    * Address data
    * @var array
    */
   public $address = array();
   
   /**
    * Estimate price
    * @var integer
    */
   public $price;
   
   /**
    * Estimate low price
    * @var integer
    */
   public $low_price;
   
   /**
    * Estimate high price
    * @var integer
    */
   public $high_price;
   
   /**
    * Estimate price change
    * @var integer
    */
   public $change;
   
   /**
    * Estimate price change duration (days)
    * @var integer
    */
   public $change_duration;
   
   
   /**
    * Constructor
    * @param SimpleXmlElement $doc xml data
    */
   public function __construct($doc)
   {
      $this->zpid = isset($doc->zpid) ? (string) $doc->zpid : null;
      
      if (isset($doc->links[0])) {
         foreach ($doc->links[0] as $link) {
            $this->links[(string) $link->getName()] = (string) $link;
         }
      }
      
      if (isset($doc->address)) {
         $this->address = array(
            'street' => isset($doc->address->street) ? (string) $doc->address->street : null,
            'city' => isset($doc->address->city) ? (string) $doc->address->city : null,
            'state' => isset($doc->address->state) ? (string) $doc->address->state : null,
            'zipcode' => isset($doc->address->zipcode) ? (string) $doc->address->zipcode : null,
            'latitude' => isset($doc->address->latitude) ? (string) $doc->address->latitude : null,
            'longitude' => isset($doc->address->longitude) ? (string) $doc->address->longitude : null,
         );
      }
      
      // valudation data:
      $this->price = isset($doc->zestimate->amount) ? (string) $doc->zestimate->amount : null;
      $this->low_price = isset($doc->zestimate->valuationRange->low) ? (string) $doc->zestimate->valuationRange->low : null;
      $this->high_price = isset($doc->zestimate->valuationRange->high) ? (string) $doc->zestimate->valuationRange->high : null;
      if (isset($doc->zestimate->valueChange)) {
         $this->change = (string) $doc->zestimate->valueChange;
         $this->change_duration = (string) $doc->zestimate->valueChange['duration'];
      }
   }
}


/**
 * Demographic data
 */
class ZillowDemographics extends ZillowData
{
   /**
    * Region data
    * @var array
    */
   public $region = array();
   
   /**
    * List of urls back to Zillow data
    * @var array
    */
   public $links = array();
   
   /**
    * List of Zillow chart urls
    * @var array
    */
   public $charts = array();
   
   /**
    * List of data metrics
    * @var array
    */
   public $metrics = array();
   
   /**
    * List of years and number of homes built
    * @var array
    */
   public $built_years = array();
   
   /**
    * List of data from US Census
    * @var array
    */
   public $census_data = array();
   
   /**
    * Data about who lives in the area
    * @var array
    */
   public $segmentation = array();
   
   /**
    * Characteristics of people living in area
    * @var array
    */
   public $characteristics = array();
   
   
   /**
    * Parses the XML from Zillow 
    * @param string $xml xml response
    */
   public function parseXml($xml)
   {
      $doc = new SimpleXmlElement($xml);
      
      $this->parseXmlStatus($doc);
      
      if (!$this->success()) {
         return;
      }
      
      $this->parseXmlRegion($doc);      
      $this->parseXmlLinks($doc);
      $this->parseXmlCharts($doc);
      
      // parse out extended information
      // some of these may not be present on each return
      if (isset($doc->response->pages->page[0])) {
         foreach ($doc->response->pages->page as $page) {
            if (isset($page->tables->table[0])) {
               foreach ($page->tables->table as $table) {
                  $this->parseXmlMetrics($table);
               }
            }
            
            // segmentation data
            if (isset($page->segmentation[0])) {
               foreach ($page->segmentation[0] as $liveshere) {
                  $this->parseXmlSegmentation($liveshere);
               }
            }
            
            // other characteristics
            if (isset($page->uniqueness[0])) {
               foreach ($page->uniqueness[0] as $category) {
                  $this->parseXmlCharacteristics($category);
               }
            }
         }
      }
   }
   
   /**
    * Parse region data from XML
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlRegion($doc)
   {
      if (isset($doc->response->region)) {
         $this->region = array(
            'id' => isset($doc->response->region->id) ? (int) $doc->response->region->id : null,
            'zip' => isset($doc->response->region->zip) ? (string) $doc->response->region->zip : null,
            'city' => isset($doc->response->region->city) ? (string) $doc->response->region->city : null,
            'state' => isset($doc->response->region->state) ? (string) $doc->response->region->state : null,
            'neighborhood' => isset($doc->response->region->neighborhood) ? (string) $doc->response->region->neighborhood : null,
            'latitude' => isset($doc->response->region->latitude) ? (string) $doc->response->region->latitude : null,
            'longitude' => isset($doc->response->region->longitude) ? (string) $doc->response->region->longitude : null,
         );
      }
   }
   
   /**
    * Parse links from XML
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlLinks($doc)
   {
      if (isset($doc->response->links[0])) {
         foreach ($doc->response->links[0] as $link) {
            $this->links[(string) $link->getName()] = (string) $link;
         }
      }
   }
   
   /**
    * Parse chart urls from XML
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlCharts($doc)
   {
      if (isset($doc->response->charts[0])) {
         foreach ($doc->response->charts[0] as $chart) {
            $this->charts[(string) $chart->name] = (string) $chart->url;
         }
      }
   }
   
   /**
    * Parse multiple metric data from xml
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlMetrics($element)
   {
      if (substr($element->name, 0, 14) == 'Census Summary') {
         // census stats
         foreach ($element->data->attribute as $attribute) {
            $this->parseXmlCensusMetric(substr($element->name, 15), $attribute);
         }
      } elseif ($element->name == 'BuiltYear') {
         // // year build spans
         foreach ($element->data->attribute as $attribute) {
            $this->parseXmlBuiltYears($attribute);
         }
      } else {
         // general/non-specialized metrics
         $key = (string) $element->name;
         $this->metrics[$key] = array();
         foreach ($element->data->attribute as $attribute) {
            list($akey, $value) = $this->parseXmlMetric($attribute);
            $this->metrics[$key][$akey] = $value; 
         }
      }
   }
   
   /**
    * Parse data from single metric
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlMetric($element)
   {
      $name = isset($element->name) ? (string) $element->name : (isset($element->values->name) ? (string) $element->values->name : null);
      if (trim($name) != '') {
         return array($name, $this->getMetricValues($element));
      }
   }
   
   /**
    * Parse census metrics from XML
    * @param string $type census data type
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlCensusMetric($type, $element)
   {
      $this->census_data[$type][(string) $element->name] = $this->getMetricValues($element);
   }
   
   /**
    * Parse built years from XML
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlBuiltYears($element)
   {
      $this->built_years[(string) $element->name] = $this->getMetricValues($element); 
   }
   
   /**
    * Parse segmentation data from XML
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlSegmentation($element)
   {
      $this->segmentation[(string) $element->title] = array(
         'title' => (string) $element->name,
         'description' => (string) $element->description,
      );
   }
   
   /**
    * Parse characteristics data from XML
    * @param SimpleXmlElement $doc Xml element
    */
   protected function parseXmlCharacteristics($element)
   {
      $this->characteristics[(string) $element['type']] = array();
      foreach ($element->characteristic as $characteristic) {
         $this->characteristics[(string) $element['type']][] = (string) $characteristic;
      }
   }
   
   /**
    * Parse information from a single metric value
    * @param SimpleXmlElement $doc Xml element
    * @return mixed metric value (array of different areas or single)
    */
   protected function getMetricValues($element)
   {
      // check for single vs multiple values
      if (isset($element->values)) {
         return array(
            'city' => isset($element->values->city->value) ? (string) $element->values->city->value : null,
            'nation' => isset($element->values->nation->value) ? (string) $element->values->nation->value : null,
            'zip' => isset($element->values->zip->value) ? (string) $element->values->zip->value : null,
         );
      } else {
         return isset($element->value) ? (string) $element->value : null;
      }
   }
}


/**
 * Region chart data
 */
class ZillowRegionChart extends ZillowData
{
   /**
    * Chart URL
    * @var string
    */
   public $url;
   
   /**
    * ZIndex value
    * @var integer
    */
   public $zindex;
   
   
   /**
    * Parses the XML from Zillow 
    * @param string $xml xml response
    */
   public function parseXml($xml)
   {
      $doc = new SimpleXmlElement($xml);
      
      $this->parseXmlStatus($doc);
      
      if (!$this->success()) {
         return;
      }
      
      $this->url = isset($doc->response->url) ? (string) $doc->response->url : null;
      $this->zindex = isset($doc->response->zindex) ? (float) $doc->response->zindex : null;
   }
}


/**
 * Region data
 */
class ZillowRegion
{
   /**
    * Region ID
    * @var integer
    */
   public $id = null;
   
   /**
    * Name
    * @var string
    */
   public $name = null;
   
   /**
    * Zillow index value (in USD)
    * @var float
    */
   public $zindex = null;
   
   /**
    * Latitude
    * @var float
    */
   public $latitude = null;
   
   /**
    * Longitude
    * @var float
    */
   public $longitude = null;
}


/**
 * Region Children data
 */
class ZillowRegionChildren extends ZillowData
{
   /**
    * Region result type
    * @var string
    */
   public $type = null;
   
   /**
    * Region
    * @var ZillowRegion
    */
   public $region = null;
   
   /**
    * Region sub-children
    * @var array
    */
   public $list = array();
   
   
   /**
    * Parse xml
    * @param string $xml xml
    */
   public function parseXml($xml)
   {
      $doc = new SimpleXmlElement($xml);
      
      $this->parseXmlStatus($doc);
      
      if (!$this->success()) {
         return;
      }
      
      $this->type = (string) $doc->response->subregiontype;
      
      if (isset($doc->response->region)) {
         $this->region = $this->parseRegionXml($doc->response->region);
      }
      
      // get sub list if available:
      if (isset($doc->response->list) && (int) $doc->response->list->count > 0) {
         foreach ($doc->response->list->region as $region) {
            $this->list[] = $this->parseRegionXml($region);
         }
      }
   }
   
   /**
    * Parse region xml
    * @param SimpleXmlElement $region_xml region element
    * @return ZillowRegion region
    */
   protected function parseRegionXml($region_xml)
   {
      $region = new ZillowRegion();
      $region->id = isset($region_xml->id) ? (string) $region_xml->id : null;
      $region->name = isset($region_xml->name) ? (string) $region_xml->name : null;
      $region->zindex = isset($region_xml->zindex) ? (string) $region_xml->zindex : null;
      $region->latitude = isset($region_xml->latitude) ? (string) $region_xml->latitude : null;
      $region->longitude = isset($region_xml->longitude) ? (string) $region_xml->longitude : null;
      
      return $region;
   }
}


/**
 * Mortgage rate summary data
 */
class ZillowRateSummary extends ZillowData
{
   /**
    * Rate table
    * @var array
    */
   public $rates = array(
      'today' => array(),
      'last_week' => array(),
   );
   
   
   /**
    * Parse xml
    * @param string $xml xml data
    */
   public function parseXml($xml)
   {
      $doc = new SimpleXmlElement($xml);
      
      $this->parseXmlStatus($doc);
      
      if (!$this->success()) {
         return;
      }
      
      // today's rates:
      foreach ($doc->response->today->rate as $rate) {
         $this->rates['today'][(string) $rate['loanType']] = (string) $rate;
      }

      // last week's rates:
      foreach ($doc->response->lastWeek->rate as $rate) {
         $this->rates['last_week'][(string) $rate['loanType']] = (string) $rate;
      }
   }
}


/**
 * Monthly payment
 */
class ZillowPayment
{
   /**
    * Loan type
    * @var string
    */
   public $type = null;
   
   /**
    * Loan rate
    * @var float
    */
   public $rate = null;
   
   /**
    * Principal and interest
    * @var float
    */
   public $pandi = null;
   
   /**
    * Mortgage insurance
    * @var float
    */
   public $pmi = null;
}


/**
 * Monthly payment estimate data
 */
class ZillowMonthlyPayments extends ZillowData
{
   /**
    * Payments
    * @var array
    */
   public $payments = array();
   
   /**
    * Property taxes (estimated)
    * @var float
    */
   public $property_taxes = null;
   
   /**
    * Insurance (estimated)
    * @var float
    */
   public $insurance = null;


   /**
    * Parse xml
    * @param string $xml xml data
    */
   public function parseXml($xml)
   {
      $doc = new SimpleXmlElement($xml);
      
      $this->parseXmlStatus($doc);
      
      if (!$this->success()) {
         return;
      }
      
      // parse the different payments out:
      foreach ($doc->response->payment as $payment) {
         $rc = new ZillowPayment();
         $rc->type = (string) $payment['loanType'];
         $rc->rate = (string) $payment->rate;
         $rc->pandi = (string) $payment->monthlyPrincipalAndInterest;
         $rc->pmi = (string) $payment->monthlyMortgageInsurance;
         
         $this->payments[] = $rc;
      }
      
      // get extras
      if (isset($doc->response->monthlyPropertyTaxes)) {
         $this->property_taxes = (float) $doc->response->monthlyPropertyTaxes;
      }
      if (isset($doc->response->monthlyHazardInsurance)) {
         $this->insurance = (float) $doc->response->monthlyHazardInsurance;
      }
   }
}
