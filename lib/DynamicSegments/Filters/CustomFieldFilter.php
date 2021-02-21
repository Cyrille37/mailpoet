<?php

namespace MailPoet\DynamicSegments\Filters;

use MailPoetVendor\Idiorm\ORM;
use MailPoet\DynamicSegments\Exceptions\InvalidSegmentTypeException;

class CustomFieldFilter implements Filter {

  const SEGMENT_TYPE = 'customField';

  /** @var int */
  private $newsletter_id;

  /** @var Array */
  private $segments;

  public function __construct( $newsletter_id, Array $segments ) {
    $this->newsletter_id = $newsletter_id;
    $this->segments = $segments;
  }

  public function toSql(ORM $orm) {
    \Artefacts\Mailpoet\Segment\Admin\Admin::debug(__METHOD__);
    throw new \RuntimeException(__METHOD__.' Not implemented');
    return $orm ;
  }

  public function toArray() {
    \Artefacts\Mailpoet\Segment\Admin\Admin::debug(__METHOD__);
    return [
      'segmentType' => self::SEGMENT_TYPE,
      'newsletter_id' => $this->newsletter_id,
      'segments' => $this->segments,
    ];
  }

  public static function createFromArray( $data )
  {
    \Artefacts\Mailpoet\Segment\Admin\Admin::debug(__METHOD__,$data);

    if (empty($data['newsletter_id'])) throw new InvalidSegmentTypeException('Missing newsletter id', InvalidSegmentTypeException::MISSING_NEWSLETTER_ID);
    if (empty($data['segments'])) throw new InvalidSegmentTypeException('Missing segments', InvalidSegmentTypeException::MISSING_SEGMENTS);

    return new CustomFieldFilter($data['newsletter_id'], $data['segments']);
  }

}