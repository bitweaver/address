<?php
// $Header$
/**
 * Address, class to hold location data and functionality for manipulating
 *
 * date created 2009
 * @author Daniel Sutcliffe <dansut@lrcnh.com>
 * @version $Revision$
 * @package address
 */

require_once(LIBERTYFORM_PKG_PATH.'LibertyForm.php');
require_once('BitAddressCountry.php');

define('BITADDRESS_CONTENT_TYPE_GUID', 'bitaddress'); // Unique identifier for the object with BW

class BitAddress extends LibertyForm {
	const CONTENT_TYPE_GUID = BITADDRESS_CONTENT_TYPE_GUID;
	const DATA_TBL = 'address_data';
	const FIELDS_TBL = 'address_fields';
	protected static $mTables = array(
		self::DATA_TBL => "
			address_id I4 PRIMARY,
			content_id I4,
			country_id I4 NOTNULL,
			postcode C(16),
			region C(64),
			town C(64),
			street2 C(64),
			street1 C(64),
			type_bits I1 DEFAULT 0,
			delivery_bits I1 DEFAULT 0",
		self::FIELDS_TBL => "
			fieldname C(16),
			country_id I4 DEFAULT 0,
			description C(16),
			helptext C(64),
			type C(16),
			typopt C(16),
			maxlen I4,
			options C(64),
			defval C(16),
			is_required C(1),
			rule C(64),
			fieldorder I4",
		);
	const DATA_TBL_SEQ = 'address_data_id_seq';
	protected static $mSequences = array(
		self::DATA_TBL_SEQ => array('start' => 1),
		);

// {{{ ---- public functions ----
	// {{{ __construct()
	/**
	 * @param int $pId database Id of exiting object of this type
	 * @param int $pContentId database Id of existing LibertyContent object
	 */
	function __construct($pId=NULL, $pContentId=NULL) {
		$this->mContentTypeGuid = self::CONTENT_TYPE_GUID;
		$this->registerContentType(self::CONTENT_TYPE_GUID, array(
			'content_type_guid'   => self::CONTENT_TYPE_GUID,
			'content_name' => 'Street Address data',
			'handler_class'       => 'BitAddress',
			'handler_package'     => 'address',
			'handler_file'        => 'BitAddress.php',
			'maintainer_url'      => 'http://www.lrcnh.com/'
		));
		// Permission setup
		$this->mViewContentPerm    = 'p_address_view';
		$this->mCreateContentPerm  = 'p_address_create';
		$this->mUpdateContentPerm  = 'p_address_update';
		$this->mAdminContentPerm   = 'p_address_admin';
		$this->mExpungeContentPerm = 'p_address_expunge';

		$this->mFields = BitAddress::setupFields();

		parent::__construct($pId, $pContentId, 'address', self::DATA_TBL, self::DATA_TBL_SEQ);
	} // }}}

	// {{{ load() get data from the database either by object or libertyContent's id
	/**
	 * If this object constructed with a valid Id then load from the DB
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	public function load() {
		$ret = parent::load();
		if($ret && isset($this->mInfo['country_id'])) $this->mFields = BitAddress::setupFields($this->mInfo['country_id']);
		return $ret;
	}
	// }}} load()

	// {{{ store() update, or create, this objects data in the database
	/**
	 * @param array $pParamHash hash of values that will be used to store the page
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	public function store(&$pParamHash) {
		if(!isset($this->mInfo['country_id']) ||
		   ($this->mInfo['country_id'] != $pParamHash['country_id'])) {
			$this->mFields = BitAddress::setupFields($pParamHash['country_id']);
		}
		return parent::store($pParamHash);
	} // }}} store()

	// {{{ getDataShort() retrieve a short string containing at least some of this objects data
	/**
	 * @return string quick summary of the objects data
	 */
	public function getDataShort() {
		$dataHash = array(
			'street1'	=> $this->mInfo['street1'],
			'street2'	=> $this->mInfo['street2'],
			'town'		=> $this->mInfo['town'],
			'region'	=> $this->mInfo['region'],
			'postcode'	=> $this->mInfo['postcode'],
			);
		return self::formatDataShort($dataHash);
	} // }}} getDataShort()
// }}} ---- end public functions

// {{{ ---- public static functions ----
// mostly to deal with the structure and set of objects in the DB, not a specific instance
	// {{{ getSchemaTables()
	public static function getSchemaTables() {
		return self::$mTables;
	} // }}} getSchemaTables()

	// {{{ getSchemaSequences()
	public static function getSchemaSequences() {
		return self::$mSequences;
	} // }}} getSchemaSequences()

	// {{{ getList() generate a list of records from content database for use in list page
	/**
	 * @param array $pParamHash
	 * @return array list of objects of this type in DB, sorted and paging dealt with.
	 */
	public static function getList(&$pParamHash) {
		global $gBitSystem;
		// this makes sure parameters used later on are set
		parent::prepGetList($pParamHash);

		$selectSql = $joinSql = $whereSql = '';
		$bindVars = array();
		array_push($bindVars, self::CONTENT_TYPE_GUID);
		parent::getServicesSql('content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars);

		// this will set $find, $sort_mode, $max_records and $offset
		extract($pParamHash);

		if(is_array($find)) {
			$whereSql .= " AND (lc.`title` IN (".implode(',',array_fill(0,count($find),'?')).")) ";
			$bindVars = array_merge($bindVars, $find);
		} elseif(is_string($find)) {
			$whereSql .= " AND (UPPER(lc.`title`) like ?) ";
			$bindVars[] = '%'.strtoupper($find).'%';
		}

		$query = "
			SELECT data.*, lc.`content_id`, lc.`title`, c.`isocode3` AS `country`
				$selectSql
			FROM `".BIT_DB_PREFIX.self::DATA_TBL."` data
				INNER JOIN `".BIT_DB_PREFIX.BitAddressCountry::DATA_TBL."` c ON (c.`country_id` = data.`country_id`)
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = data.`content_id`)
				$joinSql
			WHERE (lc.`content_type_guid` = ?)
				$whereSql
			ORDER BY ".$gBitSystem->mDb->convertSortmode($sort_mode);
		$result = $gBitSystem->mDb->query($query, $bindVars, $max_records, $offset);
		$ret = array();
		while($res = $result->fetchRow()) {
			$ret[] = array(
					'id'		=> $res['address_id'],
					'text'		=> self::formatDataShort($res),
					'country'	=> $res['country'],
					'title'		=> $res['title'],
					'display_url'=> self::getUrl(ADDRESS_PKG_URL, 'address_id', $res['address_id']),
					'edit_url'	=> self::getUrl(ADDRESS_PKG_URL, 'address_id', $res['address_id'], 'edit'),
					'remove_url'=> self::getUrl(ADDRESS_PKG_URL, 'address_id', $res['address_id'], 'remove'),
				);
		}

		$query_cant = "
			SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX.self::DATA_TBL."` data
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = data.`content_id`)
				$joinSql
			WHERE (lc.`content_type_guid` = ?)
				$whereSql";
		$pParamHash["cant"] = $gBitSystem->mDb->getOne($query_cant, $bindVars);

		// add all pagination info to pParamHash
		parent::postGetList($pParamHash);
		return $ret;
	} // }}} getList()

	// {{{ getOptions() get an array of all the countries
	/**
	 * @return array where key is the Id and value is the name of the country
	 */
	public static function getOptions() {
		global $gBitSystem;
		$query = "
			SELECT d.`address_id` AS `hash_key`, d.*
			FROM `".BIT_DB_PREFIX.self::DATA_TBL."` d ";
		$list = $gBitSystem->mDb->getAssoc($query);
		$ret = array();
		foreach($list as $id => $address) {
			$ret[$id] = self::formatDataShort($address);
		}
		return $ret;
	} // }}} getOptions()

	// {{{ setupFields() static method to setup and return the form fields array without valid object
	/**
	 * @param int $pCountryId contains country Id, zero if no country defined
	 * @return array of field details where the fieldname is the key
	 */
	public static function setupFields($pCountryId=0) {
		global $gBitSystem;
		$fields = $gBitSystem->mDb->getAssoc("
			SELECT f.`fieldname` AS `hash_key`, f.*
			FROM `".BIT_DB_PREFIX.self::FIELDS_TBL."` f
			WHERE (f.`country_id` = 0)
			");
		if($pCountryId != 0) {
			$ret = $gBitSystem->mDb->getAssoc("
				SELECT f.`fieldname` AS `hash_key`, f.*
				FROM `".BIT_DB_PREFIX.self::FIELDS_TBL."` f
				WHERE (f.`country_id` = '$pCountryId')
				");
			// Use any results to override the default field values
			foreach($ret as $key => $val) {
				$save = $fields[$key];
				$fields[$key] = $val;
				foreach(array('fieldorder') as $savekey) {
					if($fields[$key][$savekey] == NULL) $fields[$key][$savekey] = $save[$savekey];
				}
			}
		}
		uasort($fields, 'ordercmp');
		// 'fix' the fields where they may need options made into arrays, etc
		foreach($fields as $fieldname => &$field) {
			switch($field['type']) {
				case 'checkboxes':
				case 'options':
				case 'radios':
					// Need 'options' to be array with keys bit values
					if(!is_array($field['options']) && (strlen($field['options'])>2)) { // Make 'fixup' not done and have basics
						$keydelim = substr($field['options'], 0, 1);
						$optdelim = substr($field['options'], 1, 1);
						$parsable = explode($optdelim, substr($field['options'], 2));
						unset($field['options']);
						$field['options'] = array(); // Destroy string data and set up to now be an array
						if($keydelim == 'C') { // 'C' key delimiter is special, means Class
							$class = new $parsable[0];
							$dataname = $parsable[1];
							$is_active = (isset($parsable[2]) && ($parsable[2] == 'y'));
							$field['options'] =  $class->getPossibles($dataname, $is_active);
							$field['defval'] = $class->getDefault();
						} else {
							$bit = 1; // Only used if this is a magic bitfield
							foreach($parsable as $keyandval) {
								if($keydelim == '0') { // '0' key delimiter is special, means magic bitfield
									$field['options'][$bit] = $keyandval;
									$bit = $bit<<1;
								} else {
									$delimpos = strpos($keyandval, $keydelim);
									if($delimpos === FALSE) { // No delim, should be error but ...
										$field['options'][$keyandval] = '';
									} else {
										$field['options'][substr($keyandval, 0, $delimpos)] = substr($keyandval, $delimpos+1);
									}
								}
							}
						}
					}
					// If this is a 'multiple' 'options' field or 'checkboxes' 'value' may be array or bitfield
					break;
				case 'date':
					if(!empty($field['defval'])) {
						$field['defval'] = strtotime($field['defval']);
					}
					break;
				default:
					// Nothing needs doing
					break;
			}
			// Database y/n fields translated into booleans
			if($field['is_required'] == 'y') $field['required'] = TRUE;
		}
		return $fields;
	} // }}} setupFields()

	// {{{ getQuickData() quick return of this objects basic data
	/**
	 * @param int $pId the identifier for an object of this type
	 * @return array hash of this objects DB fields
	 */
	public static function getQuickData($pId) {
		global $gBitSystem;
		$query = "SELECT * FROM `".BIT_DB_PREFIX.self::DATA_TBL."` WHERE (`address_id` = ?)";
		return $gBitSystem->mDb->getRow($query, array($pId));
	} // }}} getQuickData()

	// {{{ getQuickDisplay() quick disply of object of this type without instantiating
	/**
	 * @param int $pId the identifier for an object of this type
	 * @return string quick summary of object with Id
	 */
	public static function getQuickDisplay($pId) {
		$ret = self::getQuickData($pId);
		return ($ret ? self::formatDataShort($ret) : "Unknown Address");
	} // }}} getQuickDisplay()

	// {{{ formatDataShort() given an array of fields create a short formatted display string
	/**
	 * @param $pData array object fields,
	 * @return string quick summary of the data from given fields
	 */
	public static function formatDataShort($pData) {
		$display = "";
		if(isset($pData['street1']) && !empty($pData['street1'])) {
			$display .= trim($pData['street1']);
		}
		if(isset($pData['street2']) && !empty($pData['street2'])) {
			if(!empty($display)) {
				if(substr($display, -1) != ',') $display .= ","; // add comma at end if none
				$display .= " ";
			}
			$display .= trim($pData['street2']);
		}
		if(isset($pData['town']) && !empty($pData['town'])) {
			if(!empty($display)) {
				if(substr($display, -1) != ',')  $display .= ","; // add comma at end if none
				$display .= " ";
			}
			$display .= trim($pData['town']);
		}
		if(isset($pData['region']) && !empty($pData['region'])) {
			if(!empty($display)) {
				if(substr($display, -1) != ',') $display .= ","; // add comma at end if none
				$display .= " ";
			}
			$display .= trim($pData['region']);
		}
		if(isset($pData['postcode']) && !empty($pData['postcode'])) {
			if(!empty($display)) {
				if((substr($display,-1) != ',')) $display .= ",";
				$display .= " ";
			}
			$display .= trim($pData['postcode']);
		}
		return $display;
	} // }}} formatDataShort()
// }}} ---- end public static functions ----

} // BitAddress class

// ordercomp() helper function used by uasort() call in setupFields()
function ordercmp($a, $b) {
	if($a['fieldorder'] == $b['fieldorder']) return 0;
	return ($a['fieldorder'] < $b['fieldorder']) ? -1 : 1;
}

/* vim: :set fdm=marker : */
?>
