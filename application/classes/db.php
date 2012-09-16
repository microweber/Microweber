<?php

// Query databases using PDO
class db extends SQL {

    public $pdo, $i = '`';
    static $q = array();

    function __construct($c) {
        extract($c);
        //if (DB_IS_SQLITE == true) {
        $dsn = str_replace('/', DS, $dsn);
        $dsn = str_replace('\\', DS, $dsn);
        //}
 
        if (MW_IS_INSTALLED == false) {
            return false;
        }
        $this->pdo = new PDO($dsn, $user, $pass, $args);
    }

    function column($q, $p = NULL, $k = 0) {
        return ($s = $this->query($q, $p)) ? $s->fetchColumn($k) : 0;
    }

    function row($q, $p = NULL) {
        return ($s = $this->query($q, $p)) ? $s->fetch(PDO::FETCH_OBJ) : 0;
    }

    function fetch($q, $p = NULL) {
        return ($s = $this->query($q, $p)) ? $s->fetchAll(PDO::FETCH_OBJ) : 0;
    }

    function get($q, $p = NULL) {
        $s = ($s = $this->query($q, $p)) ? $s->fetchAll(PDO::FETCH_ASSOC) : 0;
        return ($s);
    }

    function query($q, $p = NULL) {
        
        
        if (MW_IS_INSTALLED == false) {
            return false;
        }
        
        $s = $this->pdo->prepare(self::$q [] = $q);
        $s->execute($p);

        return $s;
    }

}