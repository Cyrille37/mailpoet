<?php

namespace MailPoet\Segments\DynamicSegments\Filters;

use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Models\CustomField;
use MailPoetVendor\Doctrine\DBAL\Connection;

class CustomFieldFilter implements Filter {

  /** @var EntityManager */
  private $entityManager;

  public function __construct(EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filterEntity): QueryBuilder {

    $filter_id = $filterEntity->getFilterDataParam('id');
    $segments = $filterEntity->getFilterDataParam('segments');

    $subscriberCustomFieldTable = $this->entityManager->getClassMetadata(SubscriberCustomFieldEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $cf_cache = [];

    $cfValue = [];
    $segments_count = count($segments);
    for( $seg_idx=0; $seg_idx < $segments_count; $seg_idx++ )
    {
      $seg = $segments[$seg_idx];
      $seg_cf_count = count($seg);
      $alias = 'subcf'.$seg_idx ;
      $and = '' ;
      for( $cf_idx=0; $cf_idx < $seg_cf_count; $cf_idx++ )
      {
        $seg_cf = $seg[$cf_idx];
        //\Artefacts\Mailpoet\Segment\Admin\Admin::debug(__METHOD__, 'seg_idx:', $seg_idx, 'seg_cf:', $seg_cf);

        $cf_id = str_replace( 'cf_', '', $seg_cf['cf_id'] );

        if( ! isset($cf_cache[$cf_id]) )
          $cf_cache[$cf_id] = CustomField::select(['id','type'])->findOne($cf_id);
        $cf = $cf_cache[$cf_id];

        if( $and != '' )
          $and.=' OR ';
        $and.= '('.$alias.'.custom_field_id='.$cf_id.' AND ';
        switch( $cf->type )
        {
          case 'checkbox':
            $and.= $alias.'.value=:cfValue'.count($cfValue).')';
            $cfValue[] = $seg_cf['values'] ;
            break;
          case 'select':
            $and.= $alias.'.value in (:cfValue'.count($cfValue).'))';
            $cfValue[] = $seg_cf['values'] ;
            break;
          default:
            throw new \InvalidArgumentException('CustomField type "'.$cf->type.'" is not implemented');
        }
      }

      $queryBuilder = $queryBuilder->innerJoin(
        $subscribersTable,
        $subscriberCustomFieldTable,
        $alias,
        $subscribersTable.'.id = '.$alias.'.subscriber_id AND ('.$and.')'
      );

    }

    for( $i=0; $i<count($cfValue); $i++ )
    {
      if( is_array($cfValue[$i]) )
        $queryBuilder = $queryBuilder->setParameter('cfValue'.$i, $cfValue[$i], Connection::PARAM_STR_ARRAY );  
      else
        $queryBuilder = $queryBuilder->setParameter('cfValue'.$i, $cfValue[$i] );  
    }

    \Artefacts\Mailpoet\Segment\Admin\Admin::debug(__METHOD__, $queryBuilder->getSQL() );

    /*
    switch ($customfield->type)
    {
      case 'select':
        $p = (is_array($customfield->params) ? $customfield->params : \unserialize($customfield->params) );

        $values = [];
        foreach( $customfield_value as $val )
        {
          $values[]= $p['values'][$val]['value'];
        }
        break ;
    }

    // wp_mailpoet_subscriber_custom_field1

    $queryBuilder = $queryBuilder->innerJoin(
      $subscribersTable,
      $subscriberCustomFieldTable,
      'subcf',
      $subscribersTable.'.id = subcf.subscriber_id AND subcf.`value` in (:cfvalue)'
    );

    $queryBuilder = $queryBuilder->setParameter('cfvalue', $values, Connection::PARAM_STR_ARRAY );
    */

    return $queryBuilder;
  }

}
