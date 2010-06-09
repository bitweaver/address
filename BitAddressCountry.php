<?php
// $Header$
/**
* BitAddressCountry is an object designed to represent a country and it's data.
* The object also will allow access and control of the available countries
*
* date created: 2009-07-09
* @author Daniel Sutcliffe
* @version $Revision$
* @package address
* @class BitAddressCountry
*/

require_once(KERNEL_PKG_PATH.'BitBase.php');

class BitAddressCountry extends BitBase {
	protected $mId;
	const DATA_TBL = 'country_data';
	protected static $mTables = array( 
		self::DATA_TBL => "
			country_id I4 PRIMARY,
			country_name C(64),
			isocode2 C(2),
			isocode3 C(3),
			isocoden I4,
			is_active C(1)",
		);
	const DATA_TBL_SEQ = 'country_data_id_seq';
	protected static $mSequences = array(
		self::DATA_TBL_SEQ => array('start' => 1),
		);

// {{{ ---- public functions ----
	// {{{ __construct()
	/**
	 * @param int $pId database Id of existing object of this type
	 */
	public function __construct($pId=NULL) {
		parent::__construct(get_class($this).$pId); // Make up a 'name' for inherited name attribute
		@$this->load($pId);
	} // }}} __construct()

	// {{{ isValid()
	/**
	 * @return boolean TRUE if object is valid
	 */
	public function isValid() {
		return $this->mValid;
	} // }}} isValid()

	// {{{ reload() re-get contents of this object from the database
	/**
	 * @return boolean TRUE on successful completion
	 */
	public function reload() {
		return $this->load($this->mId);
	} // }}} reload()

	// {{{ clear() remove data and make object invalid
	/**
	 * @return boolean TRUE on successful completion
	 */
	public function clear() {
		return $this->load(NULL);
	} // }}} clear()

	// {{{ store() update, or create, this objects data in the database
	/**
	 * @param array $pParamHash
	 * @return boolean TRUE on success - mErrors will contain reason(s) for failure
	 */
	public function store(&$pParamHash) {
		if($this->verifyData($pParamHash)) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX.self::DATA_TBL;
			if($this->mId) {
				$locId = array("country_id" => $this->mId);
				$result = $this->mDb->associateUpdate($table, $pParamHash['store'], $locId);
				$this->mErrors['store'] = 'Failed to store the Country in DB.';
			} else {
				$pParamHash['store']['country_id'] = $this->mDb->GenID(self::DATA_TBL_SEQ);
				$result = $this->mDb->associateInsert($table, $pParamHash['store']);
				if($result) {
					$this->mId = $pParamHash['store']['country_id'];
				} else {
					$this->mErrors['store'] = 'Failed to create Country in DB.';
				}
			}
			$this->mDb->CompleteTrans();
			$this->reload();
		} else {
		   $this->mErrors['store'] = 'Failed to save the Country, problems with data.';
		}

		return empty($this->mErrors);
	} // }}} store()
// }}} ---- public functions ----

// {{{ ---- protected functions ----
	// {{{ load() get data from the database by object id
	/**
	 * @param int $pId database Id of existing object of this type
	 * @return boolean TRUE on success - mErrors will contain reason(s) for failure
	 */
	protected function load($pId) {
		// Clear object contents and mark invalid.
		$this->mId = NULL;
		$this->mInfo = array();
		$this->mErrors = array();
		$this->mValid = FALSE;

		// If the Id we have been given is 'good' then attempt to load it from DB.
		if(@$this->verifyId($pId)) {
			$bindVars = array((int)$pId);
			$query = "SELECT * FROM `".BIT_DB_PREFIX.self::DATA_TBL."` WHERE (`country_id` = ?) ";
			$ret = $this->mDb->getRow($query, $bindVars);
			if($ret) {
				$this->mInfo = &$ret;
				$this->mId = $this->mInfo['country_id'];
				$this->mValid = TRUE;
			} else {
				$this->mErrors['load'] = 'Load failed, cannot find Country Id '.$pId.' in DB.';
			}
		} elseif(!empty($pId)) { // Empty Id is not an error, everything else is
			$this->mErrors['load'] = 'Load failed, requested Country Id ('.$pId.') is invalid.';
		}

		return $this->mValid;
	} // }}} load()

	// {{{ verifyData() make sure the data is safe to store
	/**
	 * @param int $pId database Id of existing object of this type
	 * @return boolean TRUE on success - mErrors will contain reason(s) for failure
	 */
	protected function verifyData(&$pParamHash) {
		if(!empty($pParamHash['country_id']) && @$this->verifyId($pParamHash['country_id'])) {
			if($pParamHash['country_id'] == $this->mId) {
				$pParamHash['store']['country_id'] = $pParamHash['countyr_id'];
			} else {
				$this->mErrors['country_id'] = 'Changing the Id of a record is not allowed.';
			}
		}

		if(!empty($pParamHash['country_name'])) {
			$id = self::getId($pParamHash['country_name']);
			if(empty($id) || ($id == $this->mId)) {
				$pParamHash['store']['country_name'] = trim($pParamHash['country_name']);
			} else {
				$this->mErrors['country_name'] = 'A Country with this name is already defined.';
			}
		} else {
			$this->mErrors['country_name'] = 'Country name cannot be empty.';
		}

		return empty($this->mErrors);
	} // }}} verifyData()
// }}} ---- end protected functions ----

// {{{ ---- public static functions ----
// mostly to deal with the structure and set of cruise lines in the DB, not a specific line
	// {{{ getSchemaTables()
	public static function getSchemaTables() {
		return self::$mTables;
	} // }}} getSchemaTables()

	// {{{ getSchemaSequences()
	public static function getSchemaSequences() {
		return self::$mSequences;
	} // }}} getSchemaSequences()

	// {{{ getList() generate a list of records from content database
	/**
	 * @param array $pParamHash
	 * @return array list of objects of this type in the DB, sorted, filtering and paging dealt with.
	 */
	public static function getList(&$pParamHash) {
		global $gBitSystem;
		$bindVars = Array();
		$whereSql = '';

		if(!isset($pParamHash['sort_mode'])) $pParamHash['sort_mode'] = 'country_name_asc';

		parent::prepGetList($pParamHash);

		if(isset($pParamHash['only_active'])) $whereSql .= " AND (d.`is_active` LIKE 'y') ";

		if(isset($pParamHash['find'])) {
			$whereSql .= " AND (UPPER(d.`country_name`) LIKE ?) ";
			$bindVars[] = '%'.strtoupper($pParamHash['find']).'%';
		}

		$query = "
			SELECT `country_id` AS `hash_key`, d.*
			FROM `".BIT_DB_PREFIX.self::DATA_TBL."` d
			WHERE TRUE $whereSql
			ORDER BY ".$gBitSystem->mDb->convertSortmode($pParamHash['sort_mode']);
		$result = $gBitSystem->mDb->query($query, $bindVars, $pParamHash['max_records'], $pParamHash['offset']);
		$ret = Array();
		while($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$query_cant = "
			SELECT COUNT(d.*)
			FROM `".BIT_DB_PREFIX.self::DATA_TBL."` d
			WHERE TRUE $whereSql
			";
		$pParamHash['cant'] = $gBitSystem->mDb->getOne($query_cant, $bindVars);

		parent::postGetList($pParamHash);

		return $ret;
	} // }}} getList()

	// {{{ getPossibles() get an array of countries
	/**
	 * @param boolean $onlyListActive
	 * @return array of countries with country_id as key
	 */
	public static function getPossibles($pField, $pOnlyActive=FALSE) {
		global $gBitSystem;
		$whereSql = '';
		if($pOnlyActive) {
			$whereSql = " WHERE (d.`is_active` = 'y') ";
		}
		$ret = $gBitSystem->mDb->getAssoc(
			"SELECT d.`country_id` AS `hash_key`, d.`$pField`
				FROM `".BIT_DB_PREFIX.self::DATA_TBL."` d
				$whereSql
				ORDER BY `hash_key`");
		return $ret;
	} // }}} getPossibles()

	// {{{ setDefault() get an array of countries
	/**
	 * @param $pValue int containing the id to set the default country to
	 */
	public static function setDefault($pValue) {
		global $gBitSystem;
		$gBitSystem->storeConfig('address_country_default', $pValue, ADDRESS_PKG_NAME);
		return;
	} // }}} setDefault()

	// {{{ getDefault() get the default country
	/**
	 * @return the id of the default country
	 */
	public static function getDefault() {
		global $gBitSystem;
		$defval = $gBitSystem->getConfig('address_country_default');
		return $defval;
	} // }}} getDefault()

	// {{{ setActive() set the supplied countries to active and all others inactive
	/**
	 * @param array $countryIds
	 * @return boolean of whether successful or not
	 */
	public static function setActive($pCountryIds) {
		global $gBitSystem;
		$cids = "";
		foreach($pCountryIds as $cid) {
			if(!empty($cids)) $cids .= ", ";
			$cids .= $cid;
		}
		if(!empty($cids)) {
			$gBitSystem->mDb->StartTrans();
			$result = $gBitSystem->mDb->query("UPDATE `".BIT_DB_PREFIX.self::DATA_TBL."` SET `is_active`='n'");
			$result = $gBitSystem->mDb->query("UPDATE `".BIT_DB_PREFIX.self::DATA_TBL."` SET `is_active`='y' WHERE `country_id` IN (".$cids.")");
			$gBitSystem->mDb->CompleteTrans();
			return TRUE;
		} else {
			return FALSE;
		}
	} // }}} setActive

	// {{{ getOptions() get an array of all the countries
	/**
	 * @return array where key is the Id and value is the name of the country
	 */
	public static function getOptions() {
		global $gBitSystem;
		$query = "
			SELECT `country_id` AS `hash_key`, `country_name`
			FROM `".BIT_DB_PREFIX.self::DATA_TBL."`
			ORDER BY `country_name` ASC";
		$ret = $gBitSystem->mDb->getAssoc($query);
		return $ret;
	} // }}} getOptions()

	// {{{ getText() get the text form of a country from it's numeric Id
	/**
	 * @return array where key is the Id and value is the name of the country
	 * @param int $pCountryId Identifier of country in the database
	 * @param string $
	 */
	public static function getText($pCountryId, $pField='country_name') {
		global $gBitSystem;
		$bindVars = array($pCountryId);
		$query = "SELECT `$pField` FROM `".BIT_DB_PREFIX.self::DATA_TBL."` WHERE (`country_id` = ?) ";
		$ret = $gBitSystem->mDb->getRow($query, $bindVars);
		return ($ret) ? $ret[$pField] : '';
	} // }}} getText()

	// {{{ getId() find the the first Object Id that matches an objects field value.
	/**
	 * @param string $pValue the value of of a field in the object the caller wants to get Id of
	 * @param string $pField the name of the object we the caller wants to get Id of
	 * @return int the object Id of object with the matching name, zero if none found
	 */
	public static function getId($pName, $pField='country_name') {
		global $gBitSystem;
		$bindVars = array(strtoupper(trim($pName)));
		$query = "SELECT `country_id` FROM `".BIT_DB_PREFIX.self::DATA_TBL."` WHERE (UPPER(`".$pField."`) like ?) ";
		$ret = $gBitSystem->mDb->getRow($query, $bindVars);
		return ($ret) ? $ret['country_id'] : 0;
	} // }}} getId()
// }}} ---- public static functions ----

} // BitAddressCountry class
/* vim: :set fdm=marker : */
?>
