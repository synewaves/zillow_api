<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
class GetRateSummaryTest extends PHPUnit_Framework_TestCase
{
   const RESULTS = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<RateSummary:rateSummary xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/RateSummary.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/RateSummary.xsd" xmlns:RateSummary="http://www.zillow.com/static/xsd/RateSummary.xsd"><request></request><message><text>Request successfully processed</text><code>0</code></message><response><today><rate count="178" loanType="thirtyYearFixed">4.2</rate><rate count="154" loanType="fifteenYearFixed">3.78</rate><rate count="86" loanType="fiveOneARM">3.06</rate></today><lastWeek><rate count="12630" loanType="thirtyYearFixed">4.21</rate><rate count="8818" loanType="fifteenYearFixed">3.74</rate><rate count="5128" loanType="fiveOneARM">3.11</rate></lastWeek></response></RateSummary:rateSummary><!-- H:119  T:11ms  S:457  R:Thu Sep 30 14:08:57 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const FAILURE = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<RateSummary:rateSummary xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/RateSummary.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/RateSummary.xsd" xmlns:RateSummary="http://www.zillow.com/static/xsd/RateSummary.xsd"><request></request><message><text>Error: invalid or missing ZWSID parameter</text><code>2</code></message></RateSummary:rateSummary><!-- H:117  T:1ms  S:223  R:Thu Sep 30 14:42:39 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;
   
   public function setup()
   {
      $this->options = array(
         'state' => 'wa',
      );
   }
   
   public function testHandleFailureRequest()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::FAILURE));
      
      $results = $api->getRateSummary($this->options);
      $this->assertFalse($results->success());
      $this->assertEquals('2', $results->error_code);
      $this->assertEquals('Error: invalid or missing ZWSID parameter', $results->error_message);
   }
   
   public function testGetRateSummaryRawXml()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $this->assertEquals(self::RESULTS, $api->getRateSummary($this->options, true));
   }
   
   public function testGetRateSummary()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $results = $api->getRateSummary($this->options);
      
      $this->assertType('ZillowRateSummary', $results);
      $this->assertTrue($results->success());
      $this->assertEquals(3, count($results->rates['today']));
      $this->assertEquals('3.78', $results->rates['today']['fifteenYearFixed']);
      $this->assertEquals('3.11', $results->rates['last_week']['fiveOneARM']);
   }
}
