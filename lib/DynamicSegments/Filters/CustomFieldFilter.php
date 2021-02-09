<?php

namespace MailPoet\DynamicSegments\Filters;

use MailPoetVendor\Idiorm\ORM;

class CustomFieldFilter implements Filter {

  const SEGMENT_TYPE = 'customField';

  /** @var int */
  private $customfield_id;
  private $customfield_value ;

  public function __construct($customfield_id, $customfield_value) {
    $this->customfield_id = (int)$customfield_id;
    $this->customfield_value = (array)$customfield_value;
  }

  public function toSql(ORM $orm) {
    return $orm ;
  }

  public function toArray() {
    return [
      'segmentType' => self::SEGMENT_TYPE,
      'customfield_id' => $this->customfield_id,
      'customfield_value' => $this->customfield_value,
    ];
  }

}