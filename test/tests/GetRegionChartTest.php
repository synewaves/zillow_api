<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
class GetRegionChartTest extends PHPUnit_Framework_TestCase
{
   const RESULTS = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<RegionChart:regionchart xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:RegionChart="http://www.zillow.com/static/xsd/RegionChart.xsd" xsi:schemaLocation="http://www.zillow.com/static/xsd/RegionChart.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/RegionChart.xsd"><request><city>Seattle</city><state>WA</state><unit-type>percent</unit-type><width>300</width><height>150</height></request><message><text>Request successfully processed</text><code>0</code></message><response><url>http://www.zillow.com/app?chartDuration=1year&amp;chartType=partner&amp;cityRegionId=16037&amp;countyRegionId=0&amp;height=150&amp;nationRegionId=0&amp;neighborhoodRegionId=0&amp;page=webservice%2FGetRegionChart&amp;service=chart&amp;showCity=true&amp;showPercent=true&amp;stateRegionId=0&amp;width=300&amp;zipRegionId=0</url><link>http://www.zillow.com/local-info/WA-Seattle/r_16037/</link><links><local>http://www.zillow.com/local-info/WA-Seattle-home-value/r_16037/</local><forSale>http://www.zillow.com/homes/for_sale/Seattle-WA/</forSale><forSaleByOwner>http://www.zillow.com/homes/fsbo/Seattle-WA/</forSaleByOwner></links><zindex currency="USD">369375</zindex></response></RegionChart:regionchart><!-- H:118  T:20ms  S:554  R:Thu Sep 30 12:07:07 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const FAILURE = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<RegionChart:regionchart xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:RegionChart="http://www.zillow.com/static/xsd/RegionChart.xsd" xsi:schemaLocation="http://www.zillow.com/static/xsd/RegionChart.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/RegionChart.xsd"><request><city>Seattle</city><state>WA</state><unit-type></unit-type><width>300</width><height>150</height></request><message><text>Error: invalid or missing ZWSID parameter</text><code>2</code></message></RegionChart:regionchart><!-- H:119  T:1ms  S:311  R:Thu Sep 30 14:43:37 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   public function setup()
   {
      $this->options = array(
         'state' => 'WA',
         'city' => 'Seattle',
         'unit-type' => 'percent',
         'width' => 300,
         'height' => 150,
      );
   }
   
   public function testHandleFailureRequest()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::FAILURE));
      
      $results = $api->getRegionChart($this->options);
      $this->assertFalse($results->success());
      $this->assertEquals('2', $results->error_code);
      $this->assertEquals('Error: invalid or missing ZWSID parameter', $results->error_message);
   }
   
   public function testRegionChartRawXml()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $this->assertEquals(self::RESULTS, $api->getRegionChart($this->options, true));
   }
   
   public function testRegionChart()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $results = $api->getRegionChart($this->options);
      
      $this->assertType('ZillowRegionChart', $results);
      $this->assertTrue($results->success());
      
      $this->assertEquals('http://www.zillow.com/app?chartDuration=1year&chartType=partner&cityRegionId=16037&countyRegionId=0&height=150&nationRegionId=0&neighborhoodRegionId=0&page=webservice%2FGetRegionChart&service=chart&showCity=true&showPercent=true&stateRegionId=0&width=300&zipRegionId=0', $results->url);
      $this->assertEquals('http://www.zillow.com/local-info/WA-Seattle/r_16037/', $results->link);
      $this->assertEquals('369375', $results->zindex);
   }
}
