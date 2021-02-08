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

class CustomFieldFilter implements Filter {

  /** @var EntityManager */
  private $entityManager;

  public function __construct(EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filterEntity): QueryBuilder {

    $customfield_id = $filterEntity->getFilterDataParam('customfield_id');
    $customfield_value = $filterEntity->getFilterDataParam('customfield_value');

    $customfield = CustomField::select(['id','name','type','params'])->findOne($customfield_id);
    //\error_log(print_r($customfield,true));
    \error_log(print_r($customfield->type,true));
    //\error_log(print_r($customfield->params,true));
    switch ($customfield->type)
    {
      case 'select':
        $p = (is_array($customfield->params) ? $customfield->params : \unserialize($customfield->params) );
        /*\error_log(print_r($p,true));
        foreach( $p['values'] as $i => $value )
        {
          \error_log($value['value']);
        }*/
        $customfield_value = $p['values'][$customfield_value]['value'];
        break ;
    }
    // wp_mailpoet_subscriber_custom_field1
    $subscriberCustomFieldTable = $this->entityManager->getClassMetadata(SubscriberCustomFieldEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $queryBuilder = $queryBuilder->innerJoin(
      $subscribersTable,
      $subscriberCustomFieldTable,
      'subcf',
      $subscribersTable.'.id = subcf.subscriber_id AND subcf.`value`=:cfvalue'
    );

    $queryBuilder = $queryBuilder->setParameter('cfvalue', $customfield_value);
    return $queryBuilder;
  }

}
