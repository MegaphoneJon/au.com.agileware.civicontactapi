<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2019
 *
 * Generated from /buildkit/build/wp514/wp-content/uploads/civicrm/ext/au.com.agileware.civicontact/xml/schema/CRM/Civicontact/CCAKey.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:2e49f7ff4f99008d33e818d34d36121c)
 */

/**
 * Database access object for the CCAKey entity.
 */
class CRM_Civicontact_DAO_CCAKey extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_cca_key';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique CCAKey ID
   *
   * @var int unsigned
   */
  public $id;

  /**
   * FK to Contact
   *
   * @var int unsigned
   */
  public $contact_id;

  /**
   * The hash for checksum
   *
   * @var string
   */
  public $hash;

  /**
   * @var timestamp
   */
  public $date;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_cca_key';
    parent::__construct();
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'contact_id', 'civicrm_contact', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Civicontact_ExtensionUtil::ts('Unique CCAKey ID'),
          'required' => TRUE,
          'where' => 'civicrm_cca_key.id',
          'table_name' => 'civicrm_cca_key',
          'entity' => 'CCAKey',
          'bao' => 'CRM_Civicontact_DAO_CCAKey',
          'localizable' => 0,
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Civicontact_ExtensionUtil::ts('FK to Contact'),
          'required' => TRUE,
          'where' => 'civicrm_cca_key.contact_id',
          'table_name' => 'civicrm_cca_key',
          'entity' => 'CCAKey',
          'bao' => 'CRM_Civicontact_DAO_CCAKey',
          'localizable' => 0,
        ],
        'hash' => [
          'name' => 'hash',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Civicontact_ExtensionUtil::ts('Hash'),
          'description' => CRM_Civicontact_ExtensionUtil::ts('The hash for checksum'),
          'maxlength' => 32,
          'size' => CRM_Utils_Type::MEDIUM,
          'where' => 'civicrm_cca_key.hash',
          'table_name' => 'civicrm_cca_key',
          'entity' => 'CCAKey',
          'bao' => 'CRM_Civicontact_DAO_CCAKey',
          'localizable' => 0,
        ],
        'date' => [
          'name' => 'date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => CRM_Civicontact_ExtensionUtil::ts('Date'),
          'required' => TRUE,
          'where' => 'civicrm_cca_key.date',
          'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
          'table_name' => 'civicrm_cca_key',
          'entity' => 'CCAKey',
          'bao' => 'CRM_Civicontact_DAO_CCAKey',
          'localizable' => 0,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'cca_key', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'cca_key', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}