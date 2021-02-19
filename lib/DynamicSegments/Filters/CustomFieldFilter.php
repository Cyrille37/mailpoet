<?php

namespace MailPoet\DynamicSegments\Filters;

use MailPoetVendor\Idiorm\ORM;
use MailPoet\DynamicSegments\Exceptions\InvalidSegmentTypeException;

class CustomFieldFilter implements Filter {

  const SEGMENT_TYPE = 'customField';

  private $segments;

  public function __construct( $segments ) {
    $this->segments = $segments;
  }

  public function toSql(ORM $orm) {
    return $orm ;
  }

  public function toArray() {
    return [
      'segmentType' => self::SEGMENT_TYPE,
      'segments' => $this->segments,
    ];
  }

  public static function createFromArray( $data )
  {
    //\Artefacts\Mailpoet\Segment\Admin\Admin::debug(__METHOD__,$data);

    if (empty($data['newsletter_id'])) throw new InvalidSegmentTypeException('Missing newsletter id', InvalidSegmentTypeException::MISSING_NEWSLETTER_ID);
    if (empty($data['segments'])) throw new InvalidSegmentTypeException('Missing segments', InvalidSegmentTypeException::MISSING_NEWSLETTER_ID);

    return new static($data['segments']);
  }

}