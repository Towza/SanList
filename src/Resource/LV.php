<?php
namespace SanctionsList\Resource;

use \SanctionsList\Resource;

class LV extends Resource
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
        $rows = $xml->xpath('.//ENTITY[@Type = \'P\']');
        $this->owner->debug('Processing '.count($rows).' individuals...');
        $cnt_ind = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'individual',
                'name'    => (string) $row->NAME[0]->WHOLENAME,
                'country' => (string) null, // no countries in this list
            ]);
            $model->saveAndUnload();
            $cnt_ind++;
        }
        $this->owner->debug('Individuals imported: '.$cnt_ind);

        // parse entities
        $rows = $xml->xpath('.//ENTITY[@Type = \'E\']');
        $this->owner->debug('Processing '.count($rows).' entities...');
        $cnt_ent = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'entity',
                'name'    => (string) $row->NAME[0]->WHOLENAME,
                'country' => (string) null, // no countries in this list
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
<WHOLE Date="29/03/2018">
    <ENTITY Id="1" Type="P" reg_date="2017-07-31" pdf_link="https://likumi.lv/ta/id/292553-par-finansu-ierobezojumu-noteiksanu-attieciba-uz-subjektiem-kas-saistiti-ar-korejas-tautas-demokratiskas-republikas-istenoto" programme="ZIEMEĻKOREJA" >
        <NAME Id="1" Entity_id="1" reg_date="2017-07-31" pdf_link="https://likumi.lv/ta/id/292553-par-finansu-ierobezojumu-noteiksanu-attieciba-uz-subjektiem-kas-saistiti-ar-korejas-tautas-demokratiskas-republikas-istenoto" programme="ZIEMEĻKOREJA" >
            <LASTNAME>Tai</LASTNAME>
            <FIRSTNAME>TSAI</FIRSTNAME>
            <MIDDLENAME>Hsein</MIDDLENAME>
            <WHOLENAME>TSAI Hsein Tai</WHOLENAME>
            <GENDER>VĪRIETIS</GENDER>
            <TITLE/>
            <FUNCTION/>
            <LANGUAGE/>
        </NAME>
        <NAME Id="2" Entity_id="1" reg_date="2017-07-31" pdf_link="https://likumi.lv/ta/id/292553-par-finansu-ierobezojumu-noteiksanu-attieciba-uz-subjektiem-kas-saistiti-ar-korejas-tautas-demokratiskas-republikas-istenoto" programme="ZIEMEĻKOREJA">
            <LASTNAME>H.T.</LASTNAME>
            <FIRSTNAME>TSAI</FIRSTNAME>
            <MIDDLENAME>Alex</MIDDLENAME>
            <WHOLENAME>TSAI Alex H.T.</WHOLENAME>
            <GENDER>VĪRIETIS</GENDER>
            <TITLE/>
            <FUNCTION/>
            <LANGUAGE/>
        </NAME>
        <BIRTH Id="1" Entity_id="1" reg_date="2017-07-31" pdf_link="https://likumi.lv/ta/id/292553-par-finansu-ierobezojumu-noteiksanu-attieciba-uz-subjektiem-kas-saistiti-ar-korejas-tautas-demokratiskas-republikas-istenoto" programme="ZIEMEĻKOREJA">
            <DATE>1945-08-08</DATE>
            <PLACE>Taivāna</PLACE>
            <COUNTRY>Taivāna</COUNTRY>
        </BIRTH>
        <PASSPORT Id="1" Entity_id="1" reg_date="2017-07-31" pdf_link="https://likumi.lv/ta/id/292553-par-finansu-ierobezojumu-noteiksanu-attieciba-uz-subjektiem-kas-saistiti-ar-korejas-tautas-demokratiskas-republikas-istenoto" programme="ZIEMEĻKOREJA">
            <NUMBER>131134049 (izdota Taivānā, Taivānas pilsonis)</NUMBER>
            <COUNTRY/>
        </PASSPORT>
        <PIEZIMES>Kompāniju Trans Merits Co. Ltd un Global Interface Company Inc vadītājs, ar kuru starpniecību sniedzis finanšu un materiālu palīdzību Korea Mining Development Trading Corporation, lai sekmētu Korejas Tautas Demokrātiskās Republikas politiskā režīma stiprināšanu, kā arī tādu programmu attīstību, kuras saistītas ar masu iznīcināšanas ieročiem, tai skaitā ar kodolieročiem un ballistiskajām raķetēm</PIEZIMES>
    </ENTITY>

    <ENTITY Id="3" Type="E" reg_date="2018-03-29" programme="ZIEMEĻKOREJA"  pdf_link="https://likumi.lv/ta/id/298111">
        <NAME Id="5" Entity_id="3" reg_date="2018-03-29" programme="ZIEMEĻKOREJA" >
            <WHOLENAME>Ruskor International Company Ltd.</WHOLENAME>
        </NAME>
        <ADDRESS Id="1" Entity_id="3">Drake Chambers, Tortola, British Virgin Islands</ADDRESS>
        <ADDRESS Id="2" Entity_id="3">Mosfilmovskaja 72, Maskava, Krievijas Federācija (Korejas Tautas Demokrātiskās Republikas vēstniecības adrese)</ADDRESS>
        <PIEZIMES>Veic darbību Korejas Tautas Demokrātiskās Republikas valdības labā. Ri Song-Hyok (LI, Cheng He) ir kompānijas patiesais labuma guvējs</PIEZIMES>
    </ENTITY>

</WHOLE>
*/
