<?php
namespace SanctionsList\Resource;

use \SanctionsList\Resource;

class ANO extends Resource
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
        $rows = $xml->xpath('//INDIVIDUAL');
        $this->owner->debug('Processing '.count($rows).' individuals...');
        $cnt_ind = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'individual',
                'name'    => trim(join(' ', [
                                (string) $row->FIRST_NAME,
                                (string) $row->SECOND_NAME,
                                (string) $row->THIRD_NAME,
                                (string) $row->FOURTH_NAME,
                            ])),
                'country' => (string) $row->INDIVIDUAL_ADDRESS->COUNTRY,
            ]);
            $model->saveAndUnload();
            $cnt_ind++;
        }
        $this->owner->debug('Individuals imported: '.$cnt_ind);

        // parse entities
        $rows = $xml->xpath('//ENTITY');
        $this->owner->debug('Processing '.count($rows).' entities...');
        $cnt_ent = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'entity',
                'name'    => trim((string) $row->FIRST_NAME),
                'country' => (string) $row->ENTITY_ADDRESS->COUNTRY,
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
<?xml version="1.0" encoding="UTF-8"?>
<CONSOLIDATED_LIST xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://www.un.org/sc/resources/sc-sanctions.xsd" dateGenerated="2018-12-27T19:04:20.174-05:00">
    <INDIVIDUALS>
        <INDIVIDUAL>
            <DATAID>6908543</DATAID>
            <VERSIONNUM>1</VERSIONNUM>
            <FIRST_NAME>TUAH</FIRST_NAME>
            <SECOND_NAME>FEBRIWANSYAH</SECOND_NAME>
            <UN_LIST_TYPE>Al-Qaida</UN_LIST_TYPE>
            <REFERENCE_NUMBER>QDi.393</REFERENCE_NUMBER>
            <LISTED_ON>2016-04-20</LISTED_ON>
            <COMMENTS1>Leader of an Indonesia-based organization that has publicly sworn allegiance to Islamic State in Iraq and the Levant (ISIL), listed as Al-Qaida in Iraq (QDe.115). Provided support to ISIL in the areas of recruitment, fundraising, and travel. Detained in Indonesia by Indonesian authorities as of 21 March 2015 and charged with terrorism offenses.  INTERPOL-UN Security Council Special Notice web link: https://www.interpol.int/en/notice/search/un/5943052</COMMENTS1>
            <NATIONALITY><VALUE>Indonesia</VALUE></NATIONALITY>
            <LIST_TYPE><VALUE>UN List</VALUE></LIST_TYPE>
            <LAST_DAY_UPDATED><VALUE/></LAST_DAY_UPDATED>
            <INDIVIDUAL_ALIAS><QUALITY>Good</QUALITY><ALIAS_NAME>Tuah Febriwansyah bin Arif Hasrudin</ALIAS_NAME></INDIVIDUAL_ALIAS>
            <INDIVIDUAL_ALIAS><QUALITY>Good</QUALITY><ALIAS_NAME>Tuwah Febriwansah</ALIAS_NAME></INDIVIDUAL_ALIAS>
            <INDIVIDUAL_ALIAS><QUALITY>Good</QUALITY><ALIAS_NAME>Muhammad Fachri</ALIAS_NAME></INDIVIDUAL_ALIAS>
            <INDIVIDUAL_ALIAS><QUALITY>Good</QUALITY><ALIAS_NAME>Muhammad Fachria</ALIAS_NAME></INDIVIDUAL_ALIAS>
            <INDIVIDUAL_ALIAS><QUALITY>Good</QUALITY><ALIAS_NAME>Muhammad Fachry</ALIAS_NAME></INDIVIDUAL_ALIAS>
            <INDIVIDUAL_ADDRESS>
                <STREET>Jalan Baru LUK, No.1, RT 05/07</STREET>
                <CITY>Kelurahan Bhakti Jaya, Setu Sub-district, Pamulang District</CITY>
                <STATE_PROVINCE>Tangerang Selatan, Banten Province</STATE_PROVINCE>
                <COUNTRY>Indonesia</COUNTRY>
            </INDIVIDUAL_ADDRESS>
            <INDIVIDUAL_DATE_OF_BIRTH>
                <TYPE_OF_DATE>EXACT</TYPE_OF_DATE>
                <DATE>1968-02-18</DATE>
            </INDIVIDUAL_DATE_OF_BIRTH>
            <INDIVIDUAL_PLACE_OF_BIRTH>
                <CITY>Jakarta</CITY>
                <COUNTRY>Indonesia</COUNTRY>
            </INDIVIDUAL_PLACE_OF_BIRTH>
            <INDIVIDUAL_DOCUMENT>
                <TYPE_OF_DOCUMENT>National Identification Number</TYPE_OF_DOCUMENT>
                <NUMBER>09.5004.180268.0074</NUMBER>
                <ISSUING_COUNTRY>Indonesia</ISSUING_COUNTRY>
            </INDIVIDUAL_DOCUMENT>
            <SORT_KEY/>
            <SORT_KEY_LAST_MOD/>
        </INDIVIDUAL>
    </INDIVIDUALS>
    <ENTITIES>
        <ENTITY>
            <DATAID>6908629</DATAID>
            <VERSIONNUM>1</VERSIONNUM>
            <FIRST_NAME> PROPAGANDA AND AGITATION DEPARTMENT (PAD)</FIRST_NAME>
            <UN_LIST_TYPE>DPRK</UN_LIST_TYPE>
            <REFERENCE_NUMBER>KPe.053 </REFERENCE_NUMBER>
            <LISTED_ON>2017-09-11</LISTED_ON>
            <COMMENTS1>The Propaganda and Agitation Department has full control over the media, which it uses as a tool to control the public on behalf of the DPRK leadership. The Propaganda and Agitation Department also engages in or is responsible for censorship by the Government of the DPRK, including newspaper and broadcast censorship.</COMMENTS1>
            <LIST_TYPE><VALUE>UN List</VALUE></LIST_TYPE>
            <LAST_DAY_UPDATED><VALUE/></LAST_DAY_UPDATED>
            <ENTITY_ALIAS><QUALITY/><ALIAS_NAME/></ENTITY_ALIAS>
            <ENTITY_ADDRESS>
                <CITY>Pyongyang</CITY>
                <COUNTRY>Democratic People's Republic of Korea</COUNTRY>
            </ENTITY_ADDRESS>
            <SORT_KEY/>
            <SORT_KEY_LAST_MOD/>
        </ENTITY>
    </ENTITIES>
</CONSOLIDATED_LIST>
*/
