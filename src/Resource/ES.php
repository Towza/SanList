<?php
namespace SanctionsList\Resource;

use \SanctionsList\Resource;

class ES extends Resource
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
        $xml->registerXPathNamespace('c', 'http://eu.europa.ec/fpi/fsd/export');
        $rows = $xml->xpath('.//c:sanctionEntity[./c:subjectType[@classificationCode = \'P\']]');
        $this->owner->debug('Processing '.count($rows).' individuals...');
        $cnt_ind = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'individual',
                'name'    => (string) $row->nameAlias[0]['wholeName'],
                'country' => (string) null, // no countries in this list
            ]);
            $model->saveAndUnload();
            $cnt_ind++;
        }
        $this->owner->debug('Individuals imported: '.$cnt_ind);

        // parse entities
        $xml->registerXPathNamespace('c', 'http://eu.europa.ec/fpi/fsd/export');
        $rows = $xml->xpath('.//c:sanctionEntity[./c:subjectType[@classificationCode = \'E\']]');
        $this->owner->debug('Processing '.count($rows).' entities...');
        $cnt_ent = 0;
        foreach ($rows as $row) {
            $model->set([
                'type'    => 'entity',
                'name'    => (string) $row->nameAlias[0]['wholeName'],
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
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<export xmlns="http://eu.europa.ec/fpi/fsd/export" generationDate="2018-12-21T14:39:54.511+01:00" globalFileId="118887">

    <sanctionEntity designationDetails="" unitedNationId="" euReferenceNumber="EU.36.64" logicalId="1">
        <regulation regulationType="amendment" organisationType="commission" publicationDate="2018-02-16" entryIntoForceDate="2018-02-16" numberTitle="2018/223 (OJ L43)" programme="ZWE" logicalId="110201">
            <publicationUrl>http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32018R0223&amp;from=EN</publicationUrl>
        </regulation>
        <subjectType code="person" classificationCode="P"/>
        <nameAlias firstName="Robert" middleName="Gabriel" lastName="Mugabe" wholeName="Robert Gabriel Mugabe" function="Former President" gender="M" title="" nameLanguage="" strong="true" regulationLanguage="en" logicalId="1">
            <regulationSummary regulationType="amendment" publicationDate="2018-02-16" numberTitle="2018/223 (OJ L43)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32018R0223&amp;from=EN"/>
        </nameAlias>
        <birthdate circa="false" calendarType="GREGORIAN" city="" zipCode="" birthdate="1924-02-21" dayOfMonth="21" monthOfYear="2" year="1924" region="" place="" countryIso2Code="00" countryDescription="UNKNOWN" regulationLanguage="en" logicalId="1">
            <regulationSummary regulationType="amendment" publicationDate="2005-06-16" numberTitle="898/2005 (OJ L153)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2005:153:0009:0014:EN:PDF"/>
        </birthdate>
        <identification diplomatic="false" knownExpired="false" knownFalse="false" reportedLost="false" revokedByIssuer="false" issuedBy="" latinNumber="" nameOnDocument="" number="AD001095" region="" countryIso2Code="00" countryDescription="UNKNOWN" identificationTypeCode="passport" identificationTypeDescription="National passport" regulationLanguage="en" logicalId="315">
            <remark>(passport)</remark>
            <regulationSummary regulationType="amendment" publicationDate="2012-02-22" numberTitle="151/2012 (OJ L49)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2012:049:0002:0016:EN:PDF"/>
        </identification>
    </sanctionEntity>

    <sanctionEntity designationDetails="" unitedNationId="" euReferenceNumber="EU.3523.13" logicalId="207">
        <regulation regulationType="amendment" organisationType="council" publicationDate="2018-03-22" entryIntoForceDate="2018-03-23" numberTitle="2018/468 (OJ L79)" programme="TERR" logicalId="111226">
            <publicationUrl>http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32018R0468&amp;qid=1521740406834&amp;from=EN</publicationUrl>
        </regulation>
        <subjectType code="enterprise" classificationCode="E"/>
        <nameAlias firstName="" middleName="" lastName="" wholeName="İslami Büyük Doğu Akıncılar Cephesi" function="" title="" nameLanguage="" strong="true" regulationLanguage="en" logicalId="6068">
            <regulationSummary regulationType="amendment" publicationDate="2007-12-20" numberTitle="2007/868/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2007:340:0100:0103:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Great Islamic Eastern Warriors Front" function="" title="" nameLanguage="" strong="true" regulationLanguage="en" logicalId="327">
            <regulationSummary regulationType="amendment" publicationDate="2003-12-22" numberTitle="2003/902/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2003:340:0063:0064:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Фронт на великите ислямски източни воини" function="" title="" nameLanguage="BG" strong="true" regulationLanguage="en" logicalId="5659">
            <regulationSummary regulationType="amendment" publicationDate="2007-06-29" numberTitle="2007/445/EC (OJ L169)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2007:169:0058:0062:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Grande Frente Islâmica Oriental de Combatentes" function="" title="" nameLanguage="PT" strong="true" regulationLanguage="en" logicalId="4538">
            <regulationSummary regulationType="amendment" publicationDate="2005-12-23" numberTitle="2005/930/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2005:340:0064:0066:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Fronta islamskih bojevnikov velikega vzhoda" function="" title="" nameLanguage="SL" strong="true" regulationLanguage="en" logicalId="3732">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Islamský front veľkých východných bojovníkov" function="" title="" nameLanguage="SK" strong="true" regulationLanguage="en" logicalId="3731">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Islamski Front Bojowników o Wielki Wschód" function="" title="" nameLanguage="PL" strong="true" regulationLanguage="en" logicalId="3730">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Nagy Iszlám Keleti Harci Front" function="" title="" nameLanguage="HU" strong="true" regulationLanguage="en" logicalId="3729">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Didysis islamo rytų karių frontas" function="" title="" nameLanguage="LT" strong="true" regulationLanguage="en" logicalId="3728">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Lielā Islāma Austrumu cīnītāju fronte" function="" title="" nameLanguage="LV" strong="true" regulationLanguage="en" logicalId="3727">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Suur Islami Idavõitlejate Rinne" function="" title="" nameLanguage="ET" strong="true" regulationLanguage="en" logicalId="3726">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Fronta islámských bojovníků Velkého východu" function="" title="" nameLanguage="CS" strong="true" regulationLanguage="en" logicalId="3725">
            <regulationSummary regulationType="amendment" publicationDate="2005-03-16" numberTitle="2005/221/CFSP (OJ L69)" publicationUrl="http://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32005D0221&amp;qid=1410439221593&amp;from=EN"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Fronte islamico dei combattenti del grande oriente" function="" title="" nameLanguage="IT" strong="true" regulationLanguage="en" logicalId="686">
            <regulationSummary regulationType="amendment" publicationDate="2003-12-22" numberTitle="2003/902/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2003:340:0063:0064:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Front der islamischen Kämpfer des Großen Ostens" function="" title="" nameLanguage="DE" strong="true" regulationLanguage="en" logicalId="616">
            <regulationSummary regulationType="amendment" publicationDate="2003-12-22" numberTitle="2003/902/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2003:340:0063:0064:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Μέγα Ισλαµικό Μέτωπο των Πολεµιστών της Ανατολής" function="" title="" nameLanguage="EL" strong="true" regulationLanguage="en" logicalId="521">
            <regulationSummary regulationType="amendment" publicationDate="2003-12-22" numberTitle="2003/902/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2003:340:0063:0064:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Frente de Guerreros del Gran Oriente Islámico" function="" title="" nameLanguage="ES" strong="true" regulationLanguage="en" logicalId="482">
            <regulationSummary regulationType="amendment" publicationDate="2003-12-22" numberTitle="2003/902/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2003:340:0063:0064:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Front islamique des combattants du Grand Orient" function="" title="" nameLanguage="FR" strong="true" regulationLanguage="en" logicalId="450">
            <regulationSummary regulationType="amendment" publicationDate="2003-12-22" numberTitle="2003/902/EC (OJ L340)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2003:340:0063:0064:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="IBDA-C" function="" title="" nameLanguage="" strong="true" regulationLanguage="en" logicalId="328">
            <regulationSummary regulationType="amendment" publicationDate="2004-04-02" numberTitle="2004/306/EC (OJ L99)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2004:099:0028:0029:EN:PDF"/>
        </nameAlias>
        <nameAlias firstName="" middleName="" lastName="" wholeName="Marele Front Islamic de Est al Războinicilor" function="" title="" nameLanguage="RO" strong="true" regulationLanguage="en" logicalId="5660">
            <regulationSummary regulationType="amendment" publicationDate="2007-06-29" numberTitle="2007/445/EC (OJ L169)" publicationUrl="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=OJ:L:2007:169:0058:0062:EN:PDF"/>
        </nameAlias>
    </sanctionEntity>

</export>
*/
