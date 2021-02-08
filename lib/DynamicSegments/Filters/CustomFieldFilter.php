<?php

namespace MailPoet\DynamicSegments\Filters;

use MailPoetVendor\Idiorm\ORM;

class CustomFieldFilter implements Filter {

  const SEGMENT_TYPE = 'customField';
  /** @var int */
  private $customfield_id;

  public function __construct($customfield_id) {
    $this->customfield_id = (int)$customfield_id;
  }

  public function toSql(ORM $orm) {
    return $orm ;
  }

  public function toArray() {
    return [
      'segmentType' => self::SEGMENT_TYPE,
      'customfield_id' => $this->customfield_id,
    ];
  }

}