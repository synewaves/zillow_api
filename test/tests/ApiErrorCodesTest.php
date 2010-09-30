<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
class ApiErrorCodesTest extends PHPUnit_Framework_TestCase
{
   const GENERAL_ERROR = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<MonthlyPayments:paymentsSummary xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:MonthlyPayments="http://www.zillow.com/static/xsd/MonthlyPayments.xsd" xsi:schemaLocation="http://www.zillow.com/static/xsd/MonthlyPayments.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/MonthlyPayments.xsd"><request><price>animal</price><down>15</down><zip>98104</zip></request><message><text>Error: invalid or missing 'price' parameter</text><code>500</code></message></MonthlyPayments:paymentsSummary><!-- H:120  T:1ms  S:358  R:Thu Sep 30 14:29:32 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const RATE_LIMIT = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<RegionChart:regionchart xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:RegionChart="http://www.zillow.com/static/xsd/RegionChart.xsd" xsi:schemaLocation="http://www.zillow.com/static/xsd/RegionChart.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/RegionChart.xsd"><request><city>Seattle</city><state>WA</state><unit-type>percent</unit-type><width>300</width><height>150</height></request><message><text>Request successfully processed</text><code>0</code><limit-warning>true</limit-warning></message><response><url>http://www.zillow.com/app?chartDuration=1year&amp;chartType=partner&amp;cityRegionId=16037&amp;countyRegionId=0&amp;height=150&amp;nationRegionId=0&amp;neighborhoodRegionId=0&amp;page=webservice%2FGetRegionChart&amp;service=chart&amp;showCity=true&amp;showPercent=true&amp;stateRegionId=0&amp;width=300&amp;zipRegionId=0</url><link>http://www.zillow.com/local-info/WA-Seattle/r_16037/</link><links><local>http://www.zillow.com/local-info/WA-Seattle-home-value/r_16037/</local><forSale>http://www.zillow.com/homes/for_sale/Seattle-WA/</forSale><forSaleByOwner>http://www.zillow.com/homes/fsbo/Seattle-WA/</forSaleByOwner></links><zindex currency="USD">369375</zindex></response></RegionChart:regionchart><!-- H:118  T:25ms  S:554  R:Thu Sep 30 14:48:13 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   public function setup()
   {
      $this->options = array(
         'state' => 'wa',
      );
   }

   public function testGeneralErrors()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::GENERAL_ERROR));
      
      $results = $api->getRateSummary($this->options);
      
      $this->assertFalse($results->success());
      $this->assertEquals('500', $results->error_code);
      $this->assertEquals('Error: invalid or missing \'price\' parameter', $results->error_message);
   }
   
   public function testRateLimit()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RATE_LIMIT));
      
      $results = $api->getRegionChart($this->options);
      
      $this->assertTrue($results->success());
      $this->assertTrue($results->warning_limit);
   }
   
   public function testRequestException()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->throwException(new RuntimeException));
      
      $results = $api->getRegionChart($this->options);
      
      $this->assertFalse($results->success());
      $this->assertEquals(500, $results->error_code);
      $this->assertEquals('General exception', $results->error_message);
   }
}
