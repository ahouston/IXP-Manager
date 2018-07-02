<?php

namespace Tests\Browser;

use D2EM;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

use Entities\{
    VirtualInterface    as VirtualInterfaceEntity,
    VlanInterface       as VlanInterfaceEntity,
    PhysicalInterface   as PhysicalInterfaceEntity
};

class VirtualInterfaceControllerTest extends DuskTestCase
{
    /**
     * Test the whole Interfaces functionalities (virtuel, physical, vlan)
     *
     * @return void
     *
     * @throws
     */
    public function testAddL2a()
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize(1600, 1200)
                ->visit('/auth/login')
                ->type('username', 'travis')
                ->type('password', 'travisci')
                ->press('submit')
                ->assertPathIs('/admin');


        });


    }

    /**
     * Test the Virtual interface add/edit/delete functions
     *
     * @param Browser $browser
     *
     * @return VirtualInterfaceEntity $vi
     *
     * @throws
     */
    private function intTestVi( Browser $browser )
    {

        $browser->visit('/interfaces/virtual/wizard-add/custid/5')
            ->assertSee('Virtual Interface Settings');


        // Add a new Vitural interface Via wizard form
        $browser->select('vlan', '2')
            ->check('ipv4-enabled')
            ->waitFor("#ipv4-area")
            ->check('ipv6-enabled')
            ->waitFor("#ipv6-area")
            ->select('switch', '2')
            ->waitUntilMissing("Choose a switch port")
            ->waitForText("Choose a switch port")
            ->select('switch-port', '28')
            ->select('status', '4')
            ->select('speed', '1000')
            ->select('duplex', 'full')
            ->type('maxbgpprefix', '100')
            ->check('rsclient')
            ->check('irrdbfilter')
            ->check('as112client')
            ->select('ipv4-address', "10.2.0.22")
            ->select('ipv6-address', '2001:db8:2::22')
            ->type('ipv4-hostname', 'v4.example.com')
            ->type('ipv6-hostname', 'v6.example.com')
            ->type('ipv4-bgp-md5-secret', 'soopersecret')
            ->type('ipv6-bgp-md5-secret', 'soopersecret')
            ->check('ipv4-can-ping')
            ->check('ipv6-can-ping')
            ->check('ipv4-monitor-rcbgp')
            ->check('ipv6-monitor-rcbgp')
            ->press('Save Changes')
            ->assertSee('New interface created!');

        $url = explode('/', $browser->driver->getCurrentURL());

        // Check data in DB
        /** @var $vi VirtualInterfaceEntity */
        $this->assertInstanceOf(VirtualInterfaceEntity::class, $vi = D2EM::getRepository(VirtualInterfaceEntity::class)->find(array_pop($url)));

        // check the values of the Virtual interface object
        $this->assertEquals(5, $vi->getCustomer()->getId());
        $this->assertEquals("", $vi->getName());
        $this->assertEquals(null, $vi->getMtu());
        $this->assertEquals(false, $vi->getTrunk());
        $this->assertEquals(null, $vi->getChannelgroup());
        $this->assertEquals(false, $vi->getLagFraming());
        $this->assertEquals(false, $vi->getFastLACP());

        // check that we have 1 physical interface for the virtual interface
        $this->assertEquals(1, count($vi->getVlanInterfaces()));

        // check the values of the Vlan interface object
        $vli = $vi->getVlanInterfaces()[0];
        /** @var $vli VlanInterfaceEntity */
        $this->assertEquals("10.2.0.22", $vli->getIPv4Address()->getAddress());
        $this->assertEquals("2001:db8:2::22", $vli->getIPv6Address()->getAddress());
        $this->assertEquals(2, $vli->getVlan()->getId());
        $this->assertEquals(true, $vli->getIpv4enabled());
        $this->assertEquals(true, $vli->getIpv6enabled());
        $this->assertEquals("v4.example.com", $vli->getIpv4hostname());
        $this->assertEquals("v6.example.com", $vli->getIpv6hostname());
        $this->assertEquals(false, $vli->getMcastenabled());
        $this->assertEquals(true, $vli->getIrrdbfilter());
        $this->assertEquals("soopersecret", $vli->getIpv4bgpmd5secret());
        $this->assertEquals("soopersecret", $vli->getIpv6bgpmd5secret());
        $this->assertEquals("100", $vli->getMaxbgpprefix());
        $this->assertEquals(true, $vli->getRsclient());
        $this->assertEquals(true, $vli->getIpv4canping());
        $this->assertEquals(true, $vli->getIpv6canping());
        $this->assertEquals(true, $vli->getIpv4monitorrcbgp());
        $this->assertEquals(true, $vli->getIpv6monitorrcbgp());
        $this->assertEquals(true, $vli->getAs112client());
        $this->assertEquals(false, $vli->getBusyhost());
        $this->assertEquals(null, $vli->getNotes());
        $this->assertEquals(false, $vli->getRsMoreSpecifics());


        // check that we have 1 physical interface for the virtual interface
        $this->assertEquals(1, count($vi->getPhysicalInterfaces()));

        /** @var $pi PhysicalInterfaceEntity */
        $pi = $vi->getPhysicalInterfaces()[0];

        // check the values of the Physical interface object
        $this->assertEquals("GigabitEthernet4", $pi->getSwitchPort()->getName());
        $this->assertEquals("switch2", $pi->getSwitchPort()->getSwitcher()->getName());
        $this->assertEquals(4, $pi->getStatus());
        $this->assertEquals(1000, $pi->getSpeed());
        $this->assertEquals("full", $pi->getDuplex());
        $this->assertEquals(null, $pi->getNotes());
        $this->assertEquals(true, $pi->getAutoneg());


        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Add/Edit Virtual Interface');

        // Check the form values
        $browser->assertSelected('cust', '5')
            ->assertNotChecked('trunk')
            ->assertNotChecked('lag_framing')
            ->assertNotChecked('fastlacp')
            ->click("#advanced-options")
            ->assertInputValue('name', '')
            ->assertInputValue('description', '')
            ->assertInputValue('channel-group', '')
            ->assertInputValue('mtu', '');

        // Edit the virtual Interface with new values
        $browser->select('cust', '2')
            ->check('trunk')
            ->check('lag_framing')
            ->waitFor("#fastlacp")
            ->check('fastlacp')
            ->type('name', 'name-test')
            ->type('description', 'description-test')
            ->type('channel-group', '666')
            ->type('mtu', '666')
            ->press('Save Changes')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Virtual Interface added/updated successfully.');

        // Check value in DB
        D2EM::refresh($vi);

        $this->assertEquals(2, $vi->getCustomer()->getId());
        $this->assertEquals("name-test", $vi->getName());
        $this->assertEquals(666, $vi->getMtu());
        $this->assertEquals(true, $vi->getTrunk());
        $this->assertEquals(666, $vi->getChannelgroup());
        $this->assertEquals(true, $vi->getLagFraming());
        $this->assertEquals(true, $vi->getFastLACP());

        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Add/Edit Virtual Interface');


        // Check the form with new values
        $browser->assertSelected('cust', '2')
            ->assertChecked('trunk')
            ->assertChecked('lag_framing')
            ->assertChecked('fastlacp')
            ->assertInputValue('name', 'name-test')
            ->assertInputValue('description', 'description-test')
            ->assertInputValue('channel-group', '666')
            ->assertInputValue('mtu', '666');


        // Edit the virtual Interface, uncheck all checkboxes, change value of select
        $browser->select('cust', '3')
            ->uncheck('trunk')
            ->uncheck('lag_framing')
            ->uncheck('fastlacp')
            ->press('Save Changes')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Virtual Interface added/updated successfully.');

        // Check value in DB
        D2EM::refresh($vi);

        $this->assertEquals(3, $vi->getCustomer()->getId());
        $this->assertEquals(false, $vi->getTrunk());
        $this->assertEquals(false, $vi->getLagFraming());
        $this->assertEquals(false, $vi->getFastLACP());

        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Add/Edit Virtual Interface');

        // Check the form with new values
        $browser->assertSelected('cust', '3')
            ->assertNotChecked('trunk')
            ->assertNotChecked('lag_framing')
            ->assertNotChecked('fastlacp');


        // Edit the virtual Interface, check all checkboxes
        $browser->check('trunk')
            ->check('lag_framing')
            ->waitFor("#fastlacp")
            ->check('fastlacp')
            ->press('Save Changes')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Virtual Interface added/updated successfully.');

        // Check value in DB
        D2EM::refresh($vi);

        $this->assertEquals(true, $vi->getTrunk());
        $this->assertEquals(true, $vi->getLagFraming());
        $this->assertEquals(true, $vi->getFastLACP());

        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId())
            ->assertSee('Add/Edit Virtual Interface');

        // Check the form with new values
        $browser->assertChecked('trunk')
            ->assertChecked('lag_framing')
            ->assertChecked('fastlacp');

        return $vi;
    }
}