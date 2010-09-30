<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
class GetMonthlyPaymentsTest extends PHPUnit_Framework_TestCase
{
   const RESULTS = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<MonthlyPayments:paymentsSummary xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:MonthlyPayments="http://www.zillow.com/static/xsd/MonthlyPayments.xsd" xsi:schemaLocation="http://www.zillow.com/static/xsd/MonthlyPayments.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/MonthlyPayments.xsd"><request><price>300000</price><down>15</down><zip>98104</zip></request><message><text>Request successfully processed</text><code>0</code></message><response><payment loanType="thirtyYearFixed"><rate>4.24</rate><monthlyPrincipalAndInterest>1253</monthlyPrincipalAndInterest><monthlyMortgageInsurance>81</monthlyMortgageInsurance></payment><payment loanType="fifteenYearFixed"><rate>3.74</rate><monthlyPrincipalAndInterest>1853</monthlyPrincipalAndInterest><monthlyMortgageInsurance>81</monthlyMortgageInsurance></payment><payment loanType="fiveOneARM"><rate>3.74</rate><monthlyPrincipalAndInterest>1086</monthlyPrincipalAndInterest><monthlyMortgageInsurance>81</monthlyMortgageInsurance></payment><downPayment>45000</downPayment><monthlyPropertyTaxes>193</monthlyPropertyTaxes><monthlyHazardInsurance>50</monthlyHazardInsurance></response></MonthlyPayments:paymentsSummary><!-- H:120  T:16ms  S:1549  R:Thu Sep 30 14:15:48 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const FAILURE = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<MonthlyPayments:paymentsSummary xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:MonthlyPayments="http://www.zillow.com/static/xsd/MonthlyPayments.xsd" xsi:schemaLocation="http://www.zillow.com/static/xsd/MonthlyPayments.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/MonthlyPayments.xsd"><request></request><message><text>Error: invalid or missing ZWSID parameter</text><code>2</code></message></MonthlyPayments:paymentsSummary><!-- H:120  T:1ms  S:253  R:Thu Sep 30 14:41:33 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;
   
   public function setup()
   {
      $this->api = new ZillowApi('ZILLOW_API_KEY');
      $this->options = array(
         'price' => '300000',
         'down' => '15',
         'zip' => '98104',
      );
   }
   
   public function testEmptyArguments()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getMonthlyPayments(array());
   }
   
   public function testHandleFailureRequest()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::FAILURE));
      
      $results = $api->getMonthlyPayments($this->options);
      $this->assertFalse($results->success());
      $this->assertEquals('2', $results->error_code);
      $this->assertEquals('Error: invalid or missing ZWSID parameter', $results->error_message);
   }
   
   public function testGetMonthlyPaymentsRawXml()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $this->assertEquals(self::RESULTS, $api->getMonthlyPayments($this->options, true));
   }
   
   public function testGetMonthlyPayments()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $results = $api->getMonthlyPayments($this->options);
      
      $this->assertType('ZillowMonthlyPayments', $results);
      $this->assertTrue($results->success());
      $this->assertEquals('193', $results->property_taxes);
      $this->assertEquals('50', $results->insurance);
      $this->assertEquals('45000', $results->down_payment);
      $this->assertPayments($results);
   }
   
   protected function assertPayments($results)
   {
      $this->assertEquals(3, count($results->payments));
      $this->assertType('ZillowPayment', $results->payments['thirtyYearFixed']);
      $this->assertEquals('thirtyYearFixed', $results->payments['thirtyYearFixed']->type);
      $this->assertEquals('4.24', $results->payments['thirtyYearFixed']->rate);
      $this->assertEquals('1253', $results->payments['thirtyYearFixed']->pandi);
      $this->assertEquals('81', $results->payments['thirtyYearFixed']->pmi);
   }
}
