<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
class GetSearchResultsTest extends PHPUnit_Framework_TestCase
{
   const RESULTS = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<SearchResults:searchresults xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/SearchResults.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/SearchResults.xsd" xmlns:SearchResults="http://www.zillow.com/static/xsd/SearchResults.xsd"><request><address>2114 Bigelow Ave</address><citystatezip>Seattle, WA</citystatezip></request><message><text>Request successfully processed</text><code>0</code></message><response><results><result><zpid>48749425</zpid><links><homedetails>http://www.zillow.com/homedetails/2114-Bigelow-Ave-N-Seattle-WA-98109/48749425_zpid/</homedetails><graphsanddata>http://www.zillow.com/homedetails/2114-Bigelow-Ave-N-Seattle-WA-98109/48749425_zpid/#charts-and-data</graphsanddata><mapthishome>http://www.zillow.com/homes/48749425_zpid/</mapthishome><myestimator>http://www.zillow.com/myestimator/Edit.htm?zprop=48749425</myestimator><myzestimator deprecated="true">http://www.zillow.com/myestimator/Edit.htm?zprop=48749425</myzestimator><comparables>http://www.zillow.com/homes/comps/48749425_zpid/</comparables></links><address><street>2114 Bigelow Ave N</street><zipcode>98109</zipcode><city>Seattle</city><state>WA</state><latitude>47.63793</latitude><longitude>-122.347936</longitude></address><zestimate><amount currency="USD">1022500</amount><last-updated>09/29/2010</last-updated><oneWeekChange deprecated="true"></oneWeekChange><valueChange duration="30" currency="USD">-76000</valueChange><valuationRange><low currency="USD">828225</low><high currency="USD">1124750</high></valuationRange><percentile>0</percentile></zestimate><localRealEstate></localRealEstate></result></results></response></SearchResults:searchresults><!-- H:120  T:319ms  S:1104  R:Thu Sep 30 11:02:17 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const FAILURE = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<SearchResults:searchresults xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/SearchResults.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/SearchResults.xsd" xmlns:SearchResults="http://www.zillow.com/static/xsd/SearchResults.xsd"><request><address></address><citystatezip></citystatezip></request><message><text>Error: invalid or missing ZWSID parameter</text><code>2</code></message></SearchResults:searchresults><!-- H:119  T:1ms  S:225  R:Thu Sep 30 14:46:57 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   public function setup()
   {
      $this->api = new ZillowApi('ZILLOW_API_KEY');
      $this->options = array(
         'address' => '2114 Bigelow Ave',
         'citystatezip' => 'Seattle, WA',
      );
   }
   
   public function testEmptyArguments()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getSearchResults(array());
   }
   
   public function testMissingAddress()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getSearchResults(array('citystatezip' => 'Seattle, WA'));
   }
   
   public function testMissingCityStateZip()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getSearchResults(array('address' => '123 Main Street'));
   }
   
   public function testHandleFailureRequest()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::FAILURE));
      
      $results = $api->getSearchResults($this->options);
      $this->assertFalse($results->success());
      $this->assertEquals('2', $results->error_code);
      $this->assertEquals('Error: invalid or missing ZWSID parameter', $results->error_message);
   }
   
   public function testGetSearchResultsRawXml()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $this->assertEquals(self::RESULTS, $api->getSearchResults($this->options, true));
   }
   
   public function testGetSearchResults()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $results = $api->getSearchResults($this->options);
      
      $this->assertType('ZillowSearchResults', $results);
      $this->assertTrue($results->success());
      $this->assertTrue(is_array($results->results));
      $this->assertType('ZillowSearchResult', $results->results[0]);
      $this->assertAddress($results->results[0]);
      $this->assertLinks($results->results[0]);
      $this->assertZestimate($results->results[0]);
   }
   
   protected function assertAddress($result)
   {
      $this->assertEquals('2114 Bigelow Ave N', $result->address['street']);
      $this->assertEquals('Seattle', $result->address['city']);
      $this->assertEquals('WA', $result->address['state']);
      $this->assertEquals('98109', $result->address['zipcode']);
      $this->assertEquals('47.63793', $result->address['latitude']);
      $this->assertEquals('-122.347936', $result->address['longitude']);
   }
   
   protected function assertLinks($result)
   {
      $this->assertEquals(6, count($result->links));
   }
   
   protected function assertZestimate($result)
   {
      $this->assertEquals('48749425', $result->zpid);
      $this->assertEquals('1022500', $result->price);
      $this->assertEquals('828225', $result->low_price);
      $this->assertEquals('1124750', $result->high_price);
      $this->assertEquals('-76000', $result->change);
      $this->assertEquals('30', $result->change_duration);
   }
}
