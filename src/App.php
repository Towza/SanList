<?php
namespace SanctionsList;

/**
 * App class.
 */
class App
{
    use \atk4\core\ContainerTrait;
    use \atk4\core\NameTrait;
    use \atk4\core\DIContainerTrait;
    use \atk4\core\InitializerTrait {
        init as _init;
    }
    use \atk4\core\DebugTrait;

    /** @var string Data model class */
    public $model_class = Model\Sanction::class;

    /** @var array type=>[url,parser] mapping */
    public $resources;

    /** @var \atk4\data\Persistence */
    public $db = null;

    /**
     * Initialize app.
     */
    public function init()
    {
        $this->_init();

        // debug mode?
        $this->debug($this->getConfig('debug', false));

        // load resources config
        $this->resources = $this->getConfig('resources', []);

        // connect DB
        $this->dbConnect();

        // do schema migration
        //$this->schemaMigration();
    }

    /**
     * Connect database.
     *
     * @return \atk4\data\Persistence
     */
    public function dbConnect()
    {
        if (!$this->db) {
            $dsn = $this->getConfig('dsn');

            $this->debug('Connecting Database...');
            $this->db = $this->add(\atk4\data\Persistence::connect($dsn));
            $this->debug('Database connected');
        }

        return $this->db;
    }

    /**
     * Gets config property from config.php ($config global variable)
     *
     * @param string $path
     * @param string $default_value
     *
     * @return string
     */
    public function getConfig($path, $default_value = '**undefined_value**')
    {
        global $config;

        $parts = explode('/',$path);
        $current = $config;
        foreach ($parts as $part) {
            if (!array_key_exists($part,$current)) {
                if ($default_value!=='**undefined_value**') {
                    return $default_value;
                }
                throw new Exception("You must specify \$config['".
                        join("']['",explode('/',$path)).
                        "'] in your config.php");
            } else {
                $current = $current[$part];
            }
        }

        return $current;
    }

    /**
     * Do DB schema migration.
     */
    public function schemaMigration()
    {
        $m = $this->add(new $this->model_class($this->db));
        $this->debug('Start database migration...');
        $changes = (new Migrator($m))->migrate();
        $this->debug('Migration finished: '.$changes);
    }

    /**
     * Imports data.
     *
     * @param string $type
     *
     * @return Success?
     */
    public function import(string $type)
    {
        // initialize Resource class
        $type = strtolower($type);
        if (!isset($this->resources[$type])) {
            throw new Exception(['This resource is not implemented', 'type'=>$type]);
        }
        $class = $this->resources[$type]['parser'];
        $resource = $this->add(new $class(), ['url' => $this->resources[$type]['url']]);

        // request data
        $resource->request();

        // initialize data Model and import data
        $m = $this->add(new Model\Sanction($this->db));
        $m->addCondition('list', $type); // jail in particular list
        $success = $resource->import($m);

        return $success;
    }

    /**
     * Outputs message to STDERR.
     *
     * @codeCoverageIgnore - replaced with "echo" which can be intercepted by test-suite
     *
     * @param string $message
     */
    protected function _echo_stderr($message)
    {
        file_put_contents('php://stderr', date('Y-m-d H:i:s').' '.$message);
    }
}
