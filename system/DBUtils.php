<?php
/**
 * This file is part of the db-utils package.
 * https://packagist.org/packages/a4smanjorg5/db-utils
 *
 * (c) a4smanjorg5
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID < 50207) {
    define('PHP_MAJOR_VERSION',   $version[0]);
    define('PHP_MINOR_VERSION',   $version[1]);
    define('PHP_RELEASE_VERSION', $version[2]);
}
class QUERY_FETCH {
    private $link;
    function __construct($query) {
        $this -> link = $query;
    }
    function fetch_all() {
        $args = array_merge(array($this -> link), func_get_args());
        $name = PHP_VERSION_ID < 50500 ? 'mysql' : 'mysqli';
        $name .= '_fetch_all';
        return call_user_func_array($name, $args);
    }
    function fetch_array() {
        if(PHP_VERSION_ID < 50500)
            return mysql_fetch_array($this -> link);
        return mysqli_fetch_array($this -> link);
    }
    function fetch_assoc() {
        if(PHP_VERSION_ID < 50500)
            return mysql_fetch_assoc($this -> link);
        return mysqli_fetch_assoc($this -> link);
    }
    function fetch_row() {
        if(PHP_VERSION_ID < 50500)
            return mysql_fetch_row($this -> link);
        return mysqli_fetch_row($this -> link);
    }
    function fetch_field() {
        if(PHP_VERSION_ID < 50500)
            return mysql_fetch_field($this -> link);
        return mysqli_fetch_field($this -> link);
    }
    function num_rows() {
        if(PHP_VERSION_ID < 50500)
            return mysql_num_rows($this -> link);
        return mysqli_num_rows($this -> link);
    }
}
class SQL_WHERE_CLAUSE {
    const LOGICAL_OR = "|";
    const LOGICAL_AND = "&";
    const OPERATOR_EQUALWITH = "=";
    const OPERATOR_GREATERTHAN = ">";
    const OPERATOR_GREATEROREQUAL = ">=";
    const OPERATOR_LESSTHAN = "<";
    const OPERATOR_LESSOREQUAL = "<=";
    const OPERATOR_NOTEQUAL = "!=";
    const OPERATOR_WILDCARD = "%";
    const OPERATOR_LIKEWITH = "%=";
    const OPERATOR_NOTLIKE = "!%";
    const OPERATOR_INWITH = "@";
    const OPERATOR_NOTIN = "!@";
    const OPERATOR_BETWEENWITH = "~";
    const OPERATOR_NOTBETWEEN = "!~";
    const OPERATOR_ISNULL = "#";
    const OPERATOR_NOTNULL = "!#";

    private $mysql = null;
    private $clauses = array();
    private $op_logical = self::LOGICAL_AND;
    private $last_logical = null;
    private $rev = -1;

    function next() {
        if(func_num_args() < 1) {
            if($this -> rev < 0) return false;
            $clauses = $this -> clauses;
            if(count($clauses) > 1 && is_null($this -> last_logical) &&
             $this -> rev <= 0) {
                $this -> default_logical = $clauses[1];
                $this -> last_logical = $clauses[1];
                return $clauses[1];
            }
            if(is_array($clauses[$this -> rev])) {
                $clause = $clauses[$this -> rev];
                $result = array($clause['op'], $clause['col']);
                foreach($clause as $p => $v) if($p !== "op" &&
                 $p !== "col") $result[] = $v;
            } else {
                if($this -> op_logical != $clauses[$this -> rev])
                    $this -> default_logical = $clauses[$this -> rev];
                if($this -> last_logical != $clauses[$this -> rev]) {
                    $result = $clauses[$this -> rev];
                    $this -> last_logical = $clauses[$this -> rev];
                }
            }
            $this -> rev++;
            if(count($this -> clauses) <= $this -> rev) {
                $this -> last_logical = null;
                $this -> rev = -1;
            }
            return (isset($result) ? $result : $this -> next());
        } elseif(func_num_args() <= 1)
            throw new InvalidArgumentException("Missing argument 1.");
        if($this -> rev >= 0) return false;
        $clause = array("col" => func_get_arg(0), "op" => func_get_arg(1));
        switch($clause['op']) {
            case self::OPERATOR_EQUALWITH:
            case self::OPERATOR_GREATERTHAN:
            case self::OPERATOR_GREATEROREQUAL:
            case self::OPERATOR_LESSTHAN:
            case self::OPERATOR_LESSOREQUAL:
            case self::OPERATOR_NOTEQUAL:
            case self::OPERATOR_LIKEWITH:
            case self::OPERATOR_NOTLIKE:
                if(func_num_args() > 2)
                    $clause[] = func_get_arg(2);
                else $clause[] = null;
                break;
            case self::OPERATOR_INWITH:
            case self::OPERATOR_NOTIN:
                if(func_num_args() > 2)
                    $clause[] = func_get_arg(2);
                else $clause[] = null;
                for($i = 3; $i < func_num_args(); $i++)
                    $clause[] = func_get_arg($i);
                break;
            case self::OPERATOR_BETWEENWITH:
            case self::OPERATOR_NOTBETWEEN:
                if(func_num_args() > 2)
                    $clause[] = func_get_arg(2);
                else $clause[] = null;
                if(func_num_args() > 3)
                    $clause[] = func_get_arg(3);
                else $clause[] = null;
                break;
            case self::OPERATOR_ISNULL:
            case self::OPERATOR_NOTNULL:
                break;
            default:
                return false;
        }
        if(count($this -> clauses) > 0)
            $this -> clauses[] = $this -> op_logical;
        $this -> clauses[] = $clause;
        return true;
    }
    function __get($prop) {
        if($prop == "default_logical")
            return $this -> op_logical;
    }
    function __set($prop, $val) {
        if($prop == "default_logical") {
            if($val !== self::LOGICAL_AND &&
             $val !== self::LOGICAL_OR)
                throw new Exception("invalid value");
            $this -> op_logical = $val;
        }
    }
    function reverse($reset = true) {
        if(($this -> rev === 0 && !$reset) ||
         count($this -> clauses) > 0)
            $this -> last_logical = null;
        if($this -> rev >= 0 && !$reset)
            $this -> rev = -1;
        elseif(count($this -> clauses) > 0)
            $this -> rev = 0;
    }
    function to_clause($where_clause = true, $mysql = null, $upper_clause = true) {
        $result = "";
        if (!$mysql) $mysql = $this -> mysql;
        foreach($this -> clauses as $clause) {
            if(is_array($clause)) {
                $result .= SQL::fieldname_quote($clause['col']) . " ";
                $vals = array();
                foreach($clause as $p => $v)
                    if($p !== "col" && $p !== "op")
                        $vals[] = $v;
                switch($clause['op']) {
                    case self::OPERATOR_EQUALWITH:
                    case self::OPERATOR_GREATERTHAN:
                    case self::OPERATOR_GREATEROREQUAL:
                    case self::OPERATOR_LESSTHAN:
                    case self::OPERATOR_LESSOREQUAL:
                    case self::OPERATOR_NOTEQUAL:
                        $result .= "$clause[op] " .
                         SQL::escape_valstr($vals[0], $upper_clause, $mysql);
                        break;
                    case self::OPERATOR_LIKEWITH:
                        $result .= ($upper_clause ? "LIKE" : "like") .
                         " " . SQL::value_query_quote($vals[0]);
                        break;
                    case self::OPERATOR_NOTLIKE:
                        $result .= ($upper_clause ? "NOT LIKE" : "not like") .
                         " " . SQL::value_query_quote($vals[0]);
                        break;
                    case self::OPERATOR_INWITH:
                        $result .= ($upper_clause ? "IN" : "in") .
                         "(" . implode(",", array_map(function($val) use ($upper_clause) {
                            return SQL::escape_valstr($val, $upper_clause, $mysql);
                         }, $vals)) . ")";
                        break;
                    case self::OPERATOR_NOTIN:
                        $result .= ($upper_clause ? "NOT IN" : "not in") .
                         "(" . implode(",", array_map(function($val) use ($upper_clause) {
                            return SQL::escape_valstr($val, $upper_clause, $mysql);
                         }, $vals)) . ")";
                        break;
                    case self::OPERATOR_BETWEENWITH:
                        $result .= ($upper_clause ? "BETWEEN" : "between") .
                         " " . SQL::escape_valstr($vals[0], $upper_clause, $mysql) . " " .
                         ($upper_clause ? "AND" : "and") . " " .
                         SQL::escape_valstr($vals[1], $upper_clause, $mysql);
                        break;
                    case self::OPERATOR_NOTBETWEEN:
                        $result .= ($upper_clause ? "NOT BETWEEN" : "not between") .
                         " " . SQL::escape_valstr($vals[0], $upper_clause, $mysql) . " " .
                         ($upper_clause ? "AND" : "and") . " " .
                         SQL::escape_valstr($vals[1], $upper_clause, $mysql);
                        break;
                    case self::OPERATOR_ISNULL:
                        $result .= $upper_clause ? "IS NULL" : "is null";
                        break;
                    case self::OPERATOR_NOTNULL:
                        $result .= $upper_clause ? "IS NOT NULL" : "is not null";
                        break;
                }
            } else switch($clause) {
                case self::LOGICAL_AND:
                    $result .= $upper_clause ? " AND " : " and ";
                    break;
                case self::LOGICAL_OR:
                    $result .= $upper_clause ? " OR " : " or ";
                    break;
            }
        }
        return ($where_clause ? ($upper_clause ? "WHERE" : "where") . " " .
          (empty($result) ? 1 : "") : "") . $result;
    }
    function __toString() {
        return $this -> to_clause();
    }
    private static function is_operator($value) {
        switch($value) {
            case self::OPERATOR_EQUALWITH:
            case self::OPERATOR_GREATERTHAN:
            case self::OPERATOR_GREATEROREQUAL:
            case self::OPERATOR_LESSTHAN:
            case self::OPERATOR_LESSOREQUAL:
            case self::OPERATOR_NOTEQUAL:
            case self::OPERATOR_WILDCARD:
            case self::OPERATOR_LIKEWITH:
            case self::OPERATOR_NOTLIKE:
            case self::OPERATOR_INWITH:
            case self::OPERATOR_NOTIN:
            case self::OPERATOR_BETWEENWITH:
            case self::OPERATOR_NOTBETWEEN:
            case self::OPERATOR_ISNULL:
            case self::OPERATOR_NOTNULL:
                return true;
        }
        return false;
    }
    static function create($init, $mysql = null) {
        if(!is_array($init)) return false;
        $result = new SQL_WHERE_CLAUSE();
        if (is_a($mysql, 'MYSQL'))
            $result -> mysql = $mysql;
        $co_op = false;
        foreach($init as $clause) {
            if(is_array($clause)) {
                if(count($clause) < 2) $clause[] = null;
                if(self::is_operator($clause[0]) &&
                 !self::is_operator($clause[1])) {
                    $op = $clause[0];
                    $clause[0] = $clause[1];
                    $clause[1] = $op;
                }
                if($co_op && is_null($result -> op_logical))
                    return false;
                if(!call_user_func_array(array($result, "next"), $clause))
                    return false;
                $co_op = true;
            } else try { $result -> default_logical = $clause; } catch(Exception $_) {}
        }
        return $result;
    }
}

class MYSQL {
    private $koneksi;
    private function __construct($koneksi) {
        $this -> koneksi = $koneksi;
    }
    static function connect($host = null, $username = null, $passwd = null, $port = null) {
        $args = array();
        if (!is_null($host)) $args[] = $host;
        if (!is_null($username)) $args[] = $username;
        if (!is_null($passwd)) $args[] = $passwd;
        if(PHP_VERSION_ID < 50500) {
            if (!is_null($port)) $args[0] .= ":" . (int)$port;
            $koneksi = call_user_func_array("mysql_connect", $args);
        } else {
            if (!is_null($port)) {
                $args[] = "";
                $args[] = (int)$port;
            }
            $koneksi = call_user_func_array("mysqli_connect", $args);
        }
        if($koneksi) return new MYSQL($koneksi);
    }
    function __call($name, $arguments) {
        if(PHP_VERSION_ID < 50500)
            return call_user_func_array("mysql_$name",
             array_merge($arguments, array($this -> koneksi)));
        return call_user_func_array("mysqli_$name",
         array_merge(array($this -> koneksi), $arguments));
    }
    function create_db($dbname) {
        return $this -> query("CREATE DATABASE " . SQL::fieldname_quote($dbname));
    }
    function getFields($tbl_name, $fields) {
        $res = $this -> query_select($tbl_name, $fields, 0);
        $result = array();
        while($field = $res -> fetch_field())
            $result[$field -> name] = $field;
        return $result;
    }
    function getTables($tbl_name) {
        if(func_num_args() > 1)
            $res = $this -> show_tables($tbl_name, func_get_arg(1));
        else $res = $this -> show_tables($tbl_name);
        if(!$res) return null;
        $result = array();
        while($row = $res -> fetch_row())
            $result[] = $row[0];
        return $result;
    }
    function show_tables($tbl_name) {
        $query = "SHOW TABLES";
        if(func_num_args() > 1) $query .= " FROM " .
            SQL::fieldname_quote(func_get_arg(1));
        return $this -> query($query);
    }
    function query_delete($tbl_name, $where) {
        $tbl = SQL::fieldname_quote($tbl_name);
        $w = is_a($where, "SQL_WHERE_CLAUSE") ? " " . $where -> to_clause(true, $this) : "";
        return $this -> query("DELETE FROM $tbl$w");
    }
    function query_insert($tbl_name, $values) {
        $tbl = SQL::fieldname_quote($tbl_name);
        $cols = ""; $vals = "";
        foreach($values as $col => $val) {
            $cols = "$cols" . (is_numeric($col) ? $col :
              SQL::fieldname_quote($col)) . ', ';
            $vals = "$vals" . SQL::escape_valstr($val, true, $this) . ", ";
        }
        $cols = substr($cols, 0, strlen($cols) - 2);
        $vals = substr($vals, 0, strlen($vals) - 2);
        return $this -> query("INSERT INTO $tbl ($cols) VALUES ($vals)");
    }
    private static function field_func_quote($upper_clause, $func_name) {
        if (func_num_args() > 2)
            $args = implode(',', array_map(function($val) use ($upper_clause) {
                return self::escape_fieldstr($val, $upper_clause);
            }, array_slice(func_get_args(), 2)));
        else $args = "";
        return ($upper_clause ? strtoupper($func_name) :
         strtolower($func_name)) . "($args)";
    }
    private static function escape_fieldstr($field, $upper_clause = true) {
        if (is_null($field))
            return ($upper_clause ? "NULL" : "null");
        if (is_bool($field))
            return self::escape_valstr((int)$field);
        if ((is_numeric($field) && !is_string($field)))
            return (string)$field;
        if (is_array($field))
            return call_user_func_array(array(get_class(),
             "field_func_quote"),
             array_merge(array($upper_clause), $field));
        return SQL::fieldname_quote((string)$field);
    }
    function query_select($tbl_name, $fields/*, $where|$orderBy|$descOrder|$limit|$offset, ...*/) {
        $tbl = SQL::fieldname_quote($tbl_name);
        if($fields == "*") $cols = $fields;
        else {
            $cols = "";
            foreach($fields as $fk => $fv) {
                $cols .= self::escape_fieldstr($fv);
                if (!is_numeric($fk))
                    $cols .= ' AS ' . SQL::fieldname_quote($fk);
                $cols .= ', ';
            }
            $cols = substr($cols, 0, strlen($cols) - 2);
        }
        $where = ""; $limit = ""; $offset = "";
        $orderBy = ""; $descOrder = false;
        for ($i=2; $i < func_num_args(); $i++) { 
            $arg = func_get_arg($i);
            if(is_a($arg, "SQL_WHERE_CLAUSE")) {
                if(is_a($where, "SQL_WHERE_CLAUSE")) {
                    $arg -> reverse();
                    while ($expr = $arg -> next()) {
                        if (is_array($expr)) {
                            array_splice($expr, 1, 0, array_shift($expr));
                            call_user_func_array(array($where, 'next'), $expr);
                        } else $where -> default_logical = $expr;
                    }
                } else $where = $arg;
            } elseif(is_numeric($arg)) {
                if($limit == "")
                    $limit = " LIMIT $arg";
                elseif($offset == "")
                    $offset = " OFFSET $arg";
            } elseif(is_bool($arg))
                $descOrder = $arg;
            else $orderBy = SQL::fieldname_quote($arg);
        }
        if ($orderBy != "")
            $orderBy = " ORDER BY $orderBy " . ($descOrder ? "DESC" : "ASC");
        if($where !== "") $where = ' ' . $where -> to_clause(true, $this);
        return $this -> query("SELECT $cols FROM $tbl$where$orderBy$limit$offset");
    }
    function query_update($tbl_name, $col_vals, $where = "") {
        $tbl = SQL::fieldname_quote($tbl_name);
        $vals = "";
        foreach($col_vals as $col => $val)
            $vals = "$vals" . SQL::fieldname_quote($col) .
              " = " . SQL::escape_valstr($val, true, $this) . ", ";
        $vals = substr($vals, 0, strlen($vals) - 2);
        $w = is_a($where, "SQL_WHERE_CLAUSE") ? " " . $where -> to_clause(true, $this) : "";
        return $this -> query("UPDATE $tbl SET $vals$w");
    }
    function query_truncate($tbl_name) {
        $tbl = SQL::fieldname_quote($tbl_name);
        return $this -> query("TRUNCATE TABLE $tbl");
    }
    function query($query) {
        if(PHP_VERSION_ID < 50500)
            $result = mysql_query($query, $this -> koneksi);
        else $result = mysqli_query($this -> koneksi, $query);
        if(is_bool($result)) return $result;
        return new QUERY_FETCH($result);
    }
}

class SQL {
    private function __construct() {}
    static function fieldname_quote($qn, $quotes = true) {
        if($quotes) return "`" . SQL::fieldname_quote($qn, false) . "`";
        return str_replace('`', '``', $qn);
    }
    static function value_func_quote($upper_clause, $mysql = null, $func_name) {
        if (func_num_args() > 3)
            $args = implode(',', array_map(function($val) use ($upper_clause) {
                return self::escape_valstr($val, $upper_clause, $mysql);
            }, array_slice(func_get_args(), 3)));
        else $args = "";
        return ($upper_clause ? strtoupper($func_name) :
         strtolower($func_name)) . "($args)";
    }
    static function escape_valstr($val, $upper_clause = true, $mysql = null) {
        if (is_null($val))
            return ($upper_clause ? "NULL" : "null");
        if (is_bool($val))
            return self::escape_valstr((int)$val);
        if ((is_numeric($val) && !is_string($val)))
            return (string)$val;
        if (is_array($val))
            return call_user_func_array(array(get_class(),
             "value_func_quote"),
             array_merge(array($upper_clause, $mysql), $val));
        return self::value_query_quote($val, true, $mysql);
    }
    static function value_query_quote($val, $quotes = true, $mysql = null) {
        if (!is_null($mysql) && !is_a($mysql, "MYSQL"))
            throw new InvalidArgumentException('$mysql must a MYSQL instance');
        if($quotes) return "'" . SQL::value_query_quote($val, false, $mysql) . "'";
        if (!is_null($mysql)) return $mysql -> real_escape_string((string)$val);
        return str_replace("'", "''", str_replace('\\', '\\\\', $val));
    }
}
