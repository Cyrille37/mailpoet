<?php

namespace MailPoet\DynamicSegments\Filters;

use MailPoetVendor\Idiorm\ORM;

class CustomFieldFilter implements Filter {

  const SEGMENT_TYPE = 'customField';

  /** @var int */
  private $customfield_id;
  private $customfield_value ;

  public function __construct($customfield_id, $customfield_value) {
\error_log( '__construct() '.print_r($customfield_value,true));
    $this->customfield_id = (int)$customfield_id;
    $this->customfield_value = (array)$customfield_value;
  }

  public function toSql(ORM $orm) {
    return $orm ;
  }

  public function toArray() {
    \error_log( 'toArray() '.print_r($this->customfield_value,true));
    return [
      'segmentType' => self::SEGMENT_TYPE,
      'customfield_id' => $this->customfield_id,
      'customfield_value' => $this->customfield_value,
    ];
  }

}