<?php
namespace SanctionsList\Model;

class Sanction extends \atk4\data\Model
{
    public $table = 'sanctions';

    public function init()
    {
        parent::init();

        $this->addField('list', ['required' => true]);
        $this->addField('type', ['required' => true, 'enum' => ['individual', 'entity']]);
        $this->addField('name', ['type' => 'text', 'required' => true]);
        $this->addField('country');
        $this->addField('sync_time', ['type' => 'datetime', 'default' => $this->expr('NOW()')]);
        
        $this->addExpression('tags', $this->expr('to_tsvector(\'English\',[])', [$this->getElement('name')]));
    }
}
