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

    $customfield_id = $filterEntity->getFilterDataParam('customfield_id');
    $customfield_value = $filterEntity->getFilterDataParam('customfield_value');

    $customfield = CustomField::select(['id','name','type','params'])->findOne($customfield_id);

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
    $subscriberCustomFieldTable = $this->entityManager->getClassMetadata(SubscriberCustomFieldEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $queryBuilder = $queryBuilder->innerJoin(
      $subscribersTable,
      $subscriberCustomFieldTable,
      'subcf',
      $subscribersTable.'.id = subcf.subscriber_id AND subcf.`value` in (:cfvalue)'
    );

    $queryBuilder = $queryBuilder->setParameter('cfvalue', $values, Connection::PARAM_STR_ARRAY );
    return $queryBuilder;
  }

}
