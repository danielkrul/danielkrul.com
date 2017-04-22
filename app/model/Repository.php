<?php
/**
 * Author: Daniel Krůl
 * Date: 20. 4. 2017
 * Website: http://danielkrul.com
 */
abstract class Repository extends Nette\Object {
    /** @var Nette\Database\Context */
    protected $connection;
    
    public function __construct(Nette\Database\Context $db) {
        $this->connection = $db;
    }
}