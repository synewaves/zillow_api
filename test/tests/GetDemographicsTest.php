<?php
/*
 * This file is part of the Zillow API library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 
class GetDemographicsTest extends PHPUnit_Framework_TestCase
{
   const RESULTS = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<Demographics:demographics xmlns:Demographics="http://www.zillow.com/static/xsd/Demographics.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/Demographics.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/Demographics.xsd"><request><state>WA</state><city>Seattle</city><neighborhood>Ballard</neighborhood></request><message><text>Request successfully processed</text><code>0</code></message><response><region><id>250017</id><state>Washington</state><city>Seattle</city><neighborhood>Ballard</neighborhood><latitude>47.668329</latitude><longitude>-122.384536</longitude><zmmrateurl>http://www.zillow.com/mortgage-rates/wa/seattle/</zmmrateurl></region><links><main>http://www.zillow.com/local-info/WA-Seattle/Ballard/r_250017/</main><affordability>http://www.zillow.com/local-info/WA-Seattle/Ballard-home-value/r_250017/</affordability><homesandrealestate>http://www.zillow.com/local-info/WA-Seattle/Ballard-homes/r_250017/</homesandrealestate><people>http://www.zillow.com/local-info/WA-Seattle/Ballard-people/r_250017/</people><forSale>http://www.zillow.com/homes/for_sale/Ballard-Seattle-WA/</forSale><forSaleByOwner>http://www.zillow.com/homes/fsbo/Ballard-Seattle-WA/</forSaleByOwner><foreclosures>http://www.zillow.com/homes/for_sale/Ballard-Seattle-WA/fore_lt/0_mmm/</foreclosures><recentlySold>http://www.zillow.com/homes/recently_sold/Ballard-Seattle-WA/</recentlySold></links><charts><chart><name>Median Condo Value</name><url>http://www.zillow.com/app?chartType=affordability_avgCondoValue&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name>Median Home Value</name><url>http://www.zillow.com/app?chartType=affordability_avgHomeValue&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name>Dollars Per Square Feet</name><url>http://www.zillow.com/app?chartType=affordability_pricePerSqft&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name deprecated="true">Zillow Home Value Index Distribution</name><url>http://www.zillow.com/app?chartType=affordability_ZindexByDistribution&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name>Home Type</name><url>http://www.zillow.com/app?chartType=home_homeType&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name deprecated="true">Owners vs. Renters</name><url>http://www.zillow.com/app?chartType=home_ownVsRent&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name>Home Size in Square Feet</name><url>http://www.zillow.com/app?chartType=home_homeSize&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart><chart><name>Year Built</name><url>http://www.zillow.com/app?chartType=home_yearBuilt&amp;graphType=barChart&amp;regionId=250017&amp;regionType=8&amp;service=chart</url></chart></charts><market deprecated="true"></market>        <pages>               <page><name>Affordability</name><tables><table>   <name>Affordability Data</name><data><attribute>                  <name>Zillow Home Value Index</name>                     <values>                       <neighborhood><value type="USD">327500</value></neighborhood><city><value type="USD">369400</value></city><nation><value type="USD">182200</value></nation></values>                                         </attribute><attribute>                  <name>Median Single Family Home Value</name>                     <values>                       <neighborhood><value type="USD">390600</value></neighborhood><city><value type="USD">391200</value></city><nation><value type="USD">184500</value></nation></values>                                         </attribute><attribute>                  <name>Median Condo Value</name>                     <values>                       <neighborhood><value type="USD">292700</value></neighborhood><city><value type="USD">298200</value></city><nation><value type="USD">167100</value></nation></values>                                         </attribute><attribute>                  <name>Median 2-Bedroom Home Value</name>                     <values>                       <neighborhood><value type="USD">340500</value></neighborhood><city><value type="USD">334700</value></city><nation><value type="USD">138600</value></nation></values>                                         </attribute><attribute>                  <name>Median 3-Bedroom Home Value</name>                     <values>                       <neighborhood><value type="USD">383600</value></neighborhood><city><value type="USD">393000</value></city><nation><value type="USD">168100</value></nation></values>                                         </attribute><attribute>                  <name>Median 4-Bedroom Home Value</name>                     <values>                       <neighborhood><value type="USD">418000</value></neighborhood><city><value type="USD">454100</value></city><nation><value type="USD">260800</value></nation></values>                                         </attribute><attribute>                   <name>Percent Homes Decreasing</name><values><neighborhood><value type="percent">0.668</value></neighborhood><city><value type="percent">0.783</value></city><nation><value type="percent">0.647</value></nation></values>                      </attribute><attribute>                   <name>Percent Listing Price Reduction</name><values><neighborhood><value type="percent">0.292</value></neighborhood><city><value type="percent">0.35</value></city><nation><value type="percent">0.288</value></nation></values>                      </attribute><attribute>                   <name>Median List Price Per Sq Ft</name><values><neighborhood><value type="USD">303</value></neighborhood><city><value type="USD">268</value></city><nation><value type="USD">110</value></nation></values>                      </attribute><attribute>                   <name>Median List Price</name><values><neighborhood><value type="USD">290000</value></neighborhood><city><value type="USD">385000</value></city><nation><value type="USD">184900</value></nation></values>                      </attribute><attribute>                   <name>Median Sale Price</name><values><neighborhood><value type="USD">345500</value></neighborhood><city><value type="USD">395400</value></city><nation><value type="USD">206100</value></nation></values>                      </attribute><attribute>                   <name>Homes For Sale</name><values></values>                      </attribute><attribute>                   <name>Homes Recently Sold</name><values><neighborhood><value>17</value></neighborhood><city><value>693</value></city><nation><value>236094</value></nation></values>                      </attribute><attribute>                   <name>Property Tax</name><values><neighborhood><value type="USD">2703</value></neighborhood><city><value type="USD">3302</value></city><nation><value type="USD">2217</value></nation></values>                      </attribute><attribute>                   <name>Turnover (Sold Within Last Yr.)</name><values><neighborhood><value type="percent">0.069</value></neighborhood><city><value type="percent">0.048</value></city><nation><value type="percent">0.037</value></nation></values>                      </attribute><attribute>                   <name>Median Value Per Sq Ft</name><values><neighborhood><value type="USD">349</value></neighborhood><city><value type="USD">281</value></city><nation><value type="USD">104</value></nation></values>                      </attribute><attribute>                   <name>1-Yr. Change</name><values><neighborhood><value type="percent">-0.006</value>                                     </neighborhood><city><value type="percent">-0.033</value>                                     </city><nation><value type="percent">-0.032</value>                                     </nation></values>                      </attribute><attribute>                   <name>>Homes For Sale By Owner</name><values>	                       <neighborhood>	<value>1</value> </neighborhood>                               	<city>	<value>60</value> </city>                               	<nation>	<value>11105</value> </nation>                               	</values>                      </attribute><attribute>                   <name>>New Construction</name><values>	                       <neighborhood>	<value>0</value> </neighborhood>                               	<city>	<value>24</value> </city>                               	<nation>	<value>66153</value> </nation>                               	</values>                      </attribute><attribute>                   <name>>Foreclosures</name><values>	                       <neighborhood>	<value>2</value> </neighborhood>                               	<city>	<value>265</value> </city>                               	<nation>	<value>428829</value> </nation>                               	</values>                      </attribute>           </data>      </table></tables>             </page><page><name>Homes &amp; Real Estate</name><tables><table><name>Homes &amp; Real Estate Data</name><data><attribute><name>Owners</name><values><neighborhood><value type="percent">0.35028618</value></neighborhood><city><value type="percent">0.48412441</value></city><nation><value type="percent">0.66268764</value></nation></values></attribute><attribute><name>Renters</name><values><neighborhood><value type="percent">0.64971382</value></neighborhood><city><value type="percent">0.51587559</value></city><nation><value type="percent">0.33731236</value></nation></values></attribute><attribute><name>Median Home Size (Sq. Ft.)</name><values><neighborhood><value>1110</value></neighborhood><city><value>1510</value></city></values></attribute><attribute><name>Avg. Year Built</name><values><neighborhood><value>1999</value></neighborhood><city><value>1948</value></city></values></attribute><attribute><name>Single-Family Homes</name><values><neighborhood><value type="percent">0.1928875144955</value></neighborhood><city><value type="percent">0.6960384984352</value></city></values></attribute><attribute><name>Condos</name><values><neighborhood><value type="percent">0.6969462698105</value></neighborhood><city><value type="percent">0.2616572569864</value></city></values></attribute></data></table><table><name>BuiltYear</name><data><attribute><name>&lt;1900</name><value type="percent">0.0430399379604</value></attribute><attribute><name>&gt;2000</name><value type="percent">0.5040713454827</value></attribute><attribute><name>1900-1919</name><value type="percent">0.120589375727</value></attribute><attribute><name>1920-1939</name><value type="percent">0.0407134548274</value></attribute><attribute><name>1940-1959</name><value type="percent">0.0430399379604</value></attribute><attribute><name>1960-1979</name><value type="percent">0.094998061264</value></attribute><attribute><name>1980-1999</name><value type="percent">0.1535478867778</value></attribute></data></table><table><name>Census Summary-HomeSize</name><data><attribute><name>&lt;1000sqft</name><value type="percent">0.4212034383954</value></attribute><attribute><name>&gt;3600sqft</name><value type="percent">0.0171919770773</value></attribute><attribute><name>1000-1400sqft</name><value type="percent">0.2857142857142</value></attribute><attribute><name>1400-1800sqft</name><value type="percent">0.1305771592304</value></attribute><attribute><name>1800-2400sqft</name><value type="percent">0.0790012279983</value></attribute><attribute><name>2400-3600sqft</name><value type="percent">0.065493246009</value></attribute></data></table><table><name>Census Summary-HomeType</name><data><attribute><name>Condo</name><value type="percent">0.6969462698105</value></attribute><attribute><name>Other</name><value type="percent">0.3283486320157</value></attribute><attribute><name>SingleFamily</name><value type="percent">0.1928875144955</value></attribute></data></table><table><name>Census Summary-Occupancy</name><data><attribute><name>Own</name><value type="percent">0.35028618</value></attribute><attribute><name>Rent</name><value type="percent">0.64971382</value></attribute></data></table></tables></page><page><name>People</name><tables><table><name>People Data</name><data><attribute><name>Median Household Income</name><values><neighborhood><value currency="USD">41202.9453206937</value></neighborhood><city><value currency="USD">45736</value></city><nation><value currency="USD">44512.0130806292</value></nation></values></attribute><attribute><name>Single Males</name><values><neighborhood><value type="percent">0.218182040689239</value></neighborhood><city><value type="percent">0.230033266826908</value></city><nation><value type="percent">0.146462187349365</value></nation></values></attribute><attribute><name>Single Females</name><values><neighborhood><value type="percent">0.197726979090431</value></neighborhood><city><value type="percent">0.187486853578992</value></city><nation><value type="percent">0.124578258618535</value></nation></values></attribute><attribute><name>Median Age</name><values><neighborhood><value>39</value></neighborhood><city><value>37</value></city><nation><value>36</value></nation></values></attribute><attribute><name>Homes With Kids</name><values><neighborhood><value type="percent">0.149933859172205</value></neighborhood><city><value type="percent">0.181808339938523</value></city><nation><value type="percent">0.313623902816284</value></nation></values></attribute><attribute><name>Average Household Size</name><values><neighborhood><value>1.82278897942217</value></neighborhood><city><value>2.08</value></city><nation><value>2.58883240001203</value></nation></values></attribute><attribute><name>Average Commute Time (Minutes)</name><values><neighborhood><value>26.56776121676753</value></neighborhood><city><value>26.6363786935206</value></city><nation><value>26.375545725891282</value></nation></values></attribute></data></table><table><name>Census Summary-AgeDecade</name><data><attribute><name>&gt;=70s</name><value type="percent">0.114872901061</value></attribute><attribute><name>0s</name><value type="percent">0.0698273234810158</value></attribute><attribute><name>10s</name><value type="percent">0.0614721332267584</value></attribute><attribute><name>20s</name><value type="percent">0.210411237406907</value></attribute><attribute><name>30s</name><value type="percent">0.222130722421361</value></attribute><attribute><name>40s</name><value type="percent">0.159760457231474</value></attribute><attribute><name>50s</name><value type="percent">0.100382039995932</value></attribute><attribute><name>60s</name><value type="percent">0.0611431851755522</value></attribute></data></table><table><name>Census Summary-CommuteTime</name><data><attribute><name>&lt;10min</name><value type="percent">0.116523248268039</value></attribute><attribute><name>&gt;=60min</name><value type="percent">0.0482377198229543</value></attribute><attribute><name>10-20min</name><value type="percent">0.266281330068427</value></attribute><attribute><name>20-30min</name><value type="percent">0.255069379257092</value></attribute><attribute><name>30-45min</name><value type="percent">0.189151878627933</value></attribute><attribute><name>45-60min</name><value type="percent">0.124736443955555</value></attribute></data></table><table><name>Census Summary-Household</name><data><attribute><name>NoKids</name><value type="percent">0.850066140827795</value></attribute><attribute><name>WithKids</name><value type="percent">0.149933859172205</value></attribute></data></table><table><name>Census Summary-RelationshipStatus</name><data><attribute><name>Divorced-Female</name><value type="percent">0.0854375513590899</value></attribute><attribute><name>Divorced-Male</name><value type="percent">0.0602982799519792</value></attribute><attribute><name>Married-Female</name><value type="percent">0.178297193386233</value></attribute><attribute><name>Married-Male</name><value type="percent">0.186687382837076</value></attribute><attribute><name>Single-Female</name><value type="percent">0.197726979090431</value></attribute><attribute><name>Single-Male</name><value type="percent">0.218182040689239</value></attribute><attribute><name>Widowed-Female</name><value type="percent">0.0632616593158969</value></attribute><attribute><name>Widowed-Male</name><value type="percent">0.0101089133700551</value></attribute></data></table></tables><segmentation><liveshere><title>Makin' It Singles</title><name>Upper-scale urban singles.</name><description>Pre-middle-age to middle-age singles with upper-scale incomes. May or may not own their own home. Most have college educations and are employed in mid-management professions.</description></liveshere><liveshere><title>Aspiring Urbanites</title><name>Urban singles with moderate income.</name><description>Low- to middle-income singles over a wide age range. Some have a college education. They work in a variety of occupations, including some management-level positions.</description></liveshere><liveshere><title>Bright Lights, Big City</title><name>Very mobile singles living in the city.</name><description>Singles ranging in age from early 20s to mid-40s who have moved to an urban setting. Most rent their apartment or condo. Some have a college education and work in services and the professional sector.</description></liveshere></segmentation><uniqueness><category type="Education"><characteristic>Bachelor's degrees</characteristic></category><category type="Employment"><characteristic>Females working for non-profits</characteristic><characteristic>Self-employed (unincorporated businesses)</characteristic><characteristic>Work in arts, design, entertainment, sports, or media occupations</characteristic><characteristic>Work in computer or mathematical occupations</characteristic><characteristic>Work in office and administrative support occupations</characteristic></category><category type="People &amp; Culture"><characteristic>Born in the Midwest</characteristic><characteristic>Born in the Northeast</characteristic><characteristic>Born in the South</characteristic><characteristic>Divorced females</characteristic><characteristic>Single females</characteristic><characteristic>Single males</characteristic><characteristic>Widowed females</characteristic></category><category type="Transportation"><characteristic>Get to work by bicycle</characteristic><characteristic>Get to work by bus</characteristic></category></uniqueness></page></pages></response></Demographics:demographics><!-- H:117  T:1076ms  S:41356  R:Thu Sep 30 11:18:39 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const EMPTY_LABELS = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<Demographics:demographics xmlns:Demographics="http://www.zillow.com/static/xsd/Demographics.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/Demographics.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/Demographics.xsd"><request><state>WA</state><city>Seattle</city><neighborhood>Ballard</neighborhood></request><message><text>Request successfully processed</text><code>0</code></message><response><region><id>250017</id><state>Washington</state><city>Seattle</city><neighborhood>Ballard</neighborhood><latitude>47.668329</latitude><longitude>-122.384536</longitude><zmmrateurl>http://www.zillow.com/mortgage-rates/wa/seattle/</zmmrateurl></region><pages><page><name>Affordability</name><tables><table><name>Affordability Data</name><data><attribute><name/><values><neighborhood><value type="USD">327500</value></neighborhood><city><value type="USD">369400</value></city><nation><value type="USD">182200</value></nation></values></attribute></data></table></tables></page></pages></response></Demographics:demographics><!-- H:119  T:914ms  S:41356  R:Thu Sep 30 14:51:49 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;

   const FAILURE = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<Demographics:demographics xmlns:Demographics="http://www.zillow.com/static/xsd/Demographics.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.zillow.com/static/xsd/Demographics.xsd http://www.zillowstatic.com/vstatic/5dda7c70005f4b63a372fe7f9cddeb59/static/xsd/Demographics.xsd"><request><state>WA</state><city>Seattle</city><neighborhood>Ballard</neighborhood></request><message><text>Error: invalid or missing ZWSID parameter</text><code>2</code></message></Demographics:demographics><!-- H:118  T:1ms  S:334  R:Thu Sep 30 14:37:52 PDT 2010  B:3.0.95008-comp_rel_b -->
EOT;
   
   public function setup()
   {
      $this->api = new ZillowApi('ZILLOW_API_KEY');
      $this->options = array(
         'state' => 'WA',
         'city' => 'Seattle',
         'neighborhood' => 'Ballard',
      );
   }
   
   public function testEmptyArguments()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getDemographics(array());
   }
   
   public function testJustNeighborhood()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getDemographics(array('neighborhood' => 'Ballard'));
   }
   
   public function testJustCity()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getDemographics(array('city' => 'Seattle'));
   }
   
   public function testJustState()
   {
      $this->setExpectedException('InvalidArgumentException');
      $results = $this->api->getDemographics(array('state' => 'WA'));
   }
   
   public function testHandleFailureRequest()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::FAILURE));
      
      $results = $api->getDemographics($this->options);
      $this->assertFalse($results->success());
      $this->assertEquals('2', $results->error_code);
      $this->assertEquals('Error: invalid or missing ZWSID parameter', $results->error_message);
   }
   
   public function testGetDemographicsRawXml()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $this->assertEquals(self::RESULTS, $api->getDemographics($this->options, true));
   }
   
   public function testGetDemographicsEmptyLabels()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::EMPTY_LABELS));
      
      $results = $api->getDemographics($this->options);
   
      $this->assertArrayHasKey('Affordability Data', $results->metrics);
      $this->assertEquals(0, count($results->metrics['Affordability Data']));
   }
   
   public function testGetDemographics()
   {
      $api = $this->getMock('ZillowApi', array('callWebService'), array('ZILLOW_API_KEY'));
      $api->expects($this->once())->method('callWebService')->will($this->returnValue(self::RESULTS));
      
      $results = $api->getDemographics($this->options);
      
      $this->assertType('ZillowDemographics', $results);
      $this->assertTrue($results->success());
      $this->assertRegion($results);
      $this->assertLists($results);
      $this->assertMetrics($results);
      $this->assertBuiltYears($results);
      $this->assertCensusData($results);
      $this->assertSegmentation($results);
      $this->assertCharacteristics($results);
   }
   
   protected function assertRegion($results)
   {
      $this->assertEquals('250017', $results->region['id']);
      $this->assertEquals('Washington', $results->region['state']);
      $this->assertEquals('Seattle', $results->region['city']);
      $this->assertEquals('Ballard', $results->region['neighborhood']);
      $this->assertEquals('47.668329', $results->region['latitude']);
      $this->assertEquals('-122.384536', $results->region['longitude']);
      $this->assertEquals('http://www.zillow.com/mortgage-rates/wa/seattle/', $results->region['zmmrateurl']);
   }
   
   protected function assertLists($results)
   {
      $this->assertEquals(8, count($results->links));
      $this->assertArrayHasKey('homesandrealestate', $results->links);
      $this->assertArrayHasKey('recentlySold', $results->links);
      
      $this->assertEquals(8, count($results->charts));
      $this->assertArrayHasKey('Median Home Value', $results->charts);
      $this->assertArrayHasKey('Year Built', $results->charts);
   }
   
   protected function assertMetrics($results)
   {
      $this->assertEquals(3, count($results->metrics));
      $this->assertArrayHasKey('Affordability Data', $results->metrics);
      $this->assertArrayNotHasKey('BuiltYear', $results->metrics);
      $this->assertEquals('327500', $results->metrics['Affordability Data']['Zillow Home Value Index']['neighborhood']);
      $this->assertEquals('369400', $results->metrics['Affordability Data']['Zillow Home Value Index']['city']);
      $this->assertEquals('182200', $results->metrics['Affordability Data']['Zillow Home Value Index']['nation']);
   }
   
   protected function assertBuiltYears($results)
   {
      $this->assertEquals(7, count($results->built_years));
      $this->assertArrayHasKey('1960-1979', $results->built_years);
      $this->assertEquals('0.094998061264', $results->built_years['1960-1979']);
   }
   
   protected function assertCensusData($results)
   {
      $this->assertEquals(7, count($results->census_data));
      $this->assertArrayHasKey('Household', $results->census_data);
      $this->assertArrayHasKey('NoKids', $results->census_data['Household']);
      $this->assertEquals('0.850066140827795', $results->census_data['Household']['NoKids']);
   }
   
   protected function assertSegmentation($results)
   {
      $this->assertEquals(3, count($results->segmentation));
      $this->assertArrayHasKey('Makin\' It Singles', $results->segmentation);
      $this->assertEquals('Pre-middle-age to middle-age singles with upper-scale incomes. May or may not own their own home. Most have college educations and are employed in mid-management professions.', $results->segmentation['Makin\' It Singles']['description']);
   }
   
   protected function assertCharacteristics($results)
   {
      $this->assertEquals(4, count($results->characteristics));
      $this->assertArrayHasKey('Education', $results->characteristics);
      $this->assertContains('Bachelor\'s degrees', $results->characteristics['Education']);
   }
}
