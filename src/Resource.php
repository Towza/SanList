<?php
namespace SanctionsList;

use \SanctionsList\Exception;

abstract class Resource
{
    //use \atk4\core\ContainerTrait;
    use \atk4\core\TrackableTrait;
    use \atk4\core\NameTrait;
    use \atk4\core\DIContainerTrait;
    use \atk4\core\FactoryTrait;
    use \atk4\core\InitializerTrait {
        init as _init;
    }

    /** @var string URL of resource file */
    public $url;

    /** @var string Data received from external resource */
    public $data;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->_init();
        $this->owner->debug('Resource: '.get_class($this));
    }

    /**
     * Request data from external resource.
     *
     * @return bool Success?
     */
    public function request()
    {
        $this->owner->debug('Requesting data...');
        $this->data = file_get_contents($this->url);

        if ($this->data === false) {
            $this->owner->debug('ERROR while requesting data');

            return false;
        }

        $this->owner->debug('Data received: '.strlen($this->data).' bytes');

        return true;
    }

    /**
     * Convert string to XML with better error handling.
     *
     * @param string $s
     *
     * @return SimpleXMLElement
     */
    protected function stringToXML(string $s)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($s);
        if ($xml === false) {
            $err = [];
            foreach(libxml_get_errors() as $error) {
                $err[] = $error->message;
            }
            throw new Exception(['Error while converting to XML object', 'errors'=>$err]);
        }

        return $xml;
    }

    /**
     * Import data in data model.
     *
     * @param \SanctionsList\Model\Sanction
     *
     * @return bool Success?
     */
    abstract public function import(\SanctionsList\Model\Sanction $model);
}
