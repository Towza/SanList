<?php
namespace SanctionsList\Resource;

use \SanctionsList\Resource;

class OFAC extends Resource
{
    /**
     * Import data in data model.
     *
     * @param \SanctionsList\Model\Sanction
     *
     * @return bool Success?
     */
    public function import(\SanctionsList\Model\Sanction $model)
    {
        // delete old data
        $this->owner->debug('Remove old data...');
        $model->unload(); // just in case
        $model->action('delete')->execute();
        $this->owner->debug('Old data removed');

        // import data
        $this->owner->debug('Importing data...');

        // get XML object
        $xml = $this->stringToXML($this->data);
        $this->owner->debug('Data format is correct XML');

        // parse individuals
        $xml->registerXPathNamespace('c', 'http://tempuri.org/sdnList.xsd');
        $rows = $xml->xpath('.//c:sdnEntry[./c:sdnType = \'Individual\']');
        $this->owner->debug('Processing '.count($rows).' individuals...');
        $cnt_ind = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'individual',
                'name'    => trim(join(' ', [
                                (string) $row->firstName,
                                (string) $row->lastName,
                            ])),
                'country' => (string) ($row->addressList->address ? $row->addressList->address->country : null),
            ]);
            $model->saveAndUnload();
            $cnt_ind++;
        }
        $this->owner->debug('Individuals imported: '.$cnt_ind);

        // parse entities
        $xml->registerXPathNamespace('c', 'http://tempuri.org/sdnList.xsd');
        $rows = $xml->xpath('.//c:sdnEntry[./c:sdnType = \'Entity\']');
        $this->owner->debug('Processing '.count($rows).' entities...');
        $cnt_ent = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'entity',
                'name'    => (string) $row->lastName,
                'country' => (string) ($row->addressList->address ? $row->addressList->address->country : null),
            ]);
            $model->saveAndUnload();
            $cnt_ent++;
        }
        $this->owner->debug('Entities imported: '.$cnt_ent);

        // done
        $this->owner->debug('Data imported: ' . ($cnt_ind + $cnt_ent) . ' rows');

        return true;
    }
}

/*
<?xml version="1.0" standalone="yes"?>
<sdnList xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://tempuri.org/sdnList.xsd">
  <publshInformation>
    <Publish_Date>12/19/2018</Publish_Date>
    <Record_Count>7379</Record_Count>
  </publshInformation>

  <sdnEntry>
    <uid>36</uid>
    <lastName>AEROCARIBBEAN AIRLINES</lastName>
    <sdnType>Entity</sdnType>
    <programList>
      <program>CUBA</program>
    </programList>
    <akaList>
      <aka>
        <uid>12</uid>
        <type>a.k.a.</type>
        <category>strong</category>
        <lastName>AERO-CARIBBEAN</lastName>
      </aka>
    </akaList>
    <addressList>
      <address>
        <uid>25</uid>
        <city>Havana</city>
        <country>Cuba</country>
      </address>
    </addressList>
  </sdnEntry>

  <sdnEntry>
    <uid>26235</uid>
    <firstName>Alexander Aleksandrovich</firstName>
    <lastName>MALKEVICH</lastName>
    <sdnType>Individual</sdnType>
    <remarks>(Linked To: USA REALLY)</remarks>
    <programList>
      <program>CYBER2</program>
    </programList>
    <idList>
      <id>
        <uid>16915</uid>
        <idType>Passport</idType>
        <idNumber>717637093</idNumber>
        <idCountry>Russia</idCountry>
      </id>
      <id>
        <uid>16916</uid>
        <idType>National ID No.</idType>
        <idNumber>781005202108</idNumber>
      </id>
      <id>
        <uid>130679</uid>
        <idType>Gender</idType>
        <idNumber>Male</idNumber>
      </id>
    </idList>
    <addressList>
      <address>
        <uid>39824</uid>
        <city>St. Petersburg</city>
        <country>Russia</country>
      </address>
    </addressList>
    <dateOfBirthList>
      <dateOfBirthItem>
        <uid>30676</uid>
        <dateOfBirth>14 Jun 1975</dateOfBirth>
        <mainEntry>true</mainEntry>
      </dateOfBirthItem>
    </dateOfBirthList>
    <placeOfBirthList>
      <placeOfBirthItem>
        <uid>30678</uid>
        <placeOfBirth>Leningrad, Russia</placeOfBirth>
        <mainEntry>true</mainEntry>
      </placeOfBirthItem>
    </placeOfBirthList>
  </sdnEntry>
</sdnList>
*/
