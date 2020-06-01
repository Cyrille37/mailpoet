<?php

namespace MailPoet\Test\Acceptance;

class SubscriberManageImportExportCest {
  public function importBigUsersListCSV(\AcceptanceTester $i) {
    $i->wantTo('Import a big list');
    $i->login();
    $i->amOnUrl(\AcceptanceTester::WP_URL . '/wp-admin/admin.php?page=mailpoet-import');
    $this->proceedThroughClearout($i);
    $this->uploadCsvFile($i, 'MailPoetImportBigList.csv');

    // I see validation step, select a wrong source and should be blocked
    $i->waitForElement('[data-automation-id="mailpoet_import_validation_step"]');
    $i->checkOption('[data-automation-id="mailpoet_import_validation_step_option2"]');
    $i->click('[data-automation-id="import-next-step"]');
    $i->waitForElement('[data-automation-id="import_wrong_source_block"]');

    // Repeat the test, this time choose the right source, but say you sent to the list long time ago
    $i->amOnUrl(\AcceptanceTester::WP_URL . '/wp-admin/admin.php?page=mailpoet-import');
    $this->proceedThroughClearout($i);
    $this->uploadCsvFile($i, 'MailPoetImportBigList.csv');
    $i->waitForElement('[data-automation-id="mailpoet_import_validation_step"]');
    $i->checkOption('[data-automation-id="mailpoet_import_validation_step_option1"]');
    $i->click('[data-automation-id="import-next-step"]');
    $i->waitForElement('[data-automation-id="last_sent_to_list"]');
    $i->selectOption('[data-automation-id="last_sent_to_list"]', 'over2years');
    $i->click('[data-automation-id="last_sent_to_list_next"]');
    $i->waitForElement('[data-automation-id="import_old_list_block"]');

    // Repeat the test, happy path
    $i->amOnUrl(\AcceptanceTester::WP_URL . '/wp-admin/admin.php?page=mailpoet-import');
    $this->proceedThroughClearout($i);
    $this->uploadCsvFile($i, 'MailPoetImportBigList.csv');
    $i->waitForElement('[data-automation-id="mailpoet_import_validation_step"]');
    $i->checkOption('[data-automation-id="mailpoet_import_validation_step_option1"]');
    $i->click('[data-automation-id="import-next-step"]');
    $i->waitForElement('[data-automation-id="last_sent_to_list"]');
    $i->selectOption('[data-automation-id="last_sent_to_list"]', 'less3months');
    $i->click('[data-automation-id="last_sent_to_list_next"]');
    $i->waitForElement('[data-automation-id="import_data_manipulation_step"]');
  }

  public function importUsersToSubscribersViaCSV(\AcceptanceTester $i) {
    $i->wantTo('Import a subscriber list from CSV');
    $i->login();
    $i->amOnMailPoetPage ('Subscribers');
    $i->click('[data-automation-id="import-subscribers-button"]');
    $this->proceedThroughClearout($i);
    $this->uploadCsvFile($i);
    $i->waitForText('2 records had issues and were skipped');
    $i->click('[data-automation-id="show-more-details"]');
    $i->waitForText('1 emails are not valid:');
    $i->waitForText('1 role-based addresses are not permitted');
    $this->chooseListAndConfirm($i);
    $i->see('9 subscribers added to');
    // Test reimporting the same list
    $i->click('Import again');
    $this->uploadCsvFile($i);
    $this->chooseListAndConfirm($i);
    $i->see('9 existing subscribers were updated and added to');

    //confirm subscribers from import list were added
    $i->amOnMailPoetPage ('Subscribers');
    $i->searchFor('aaa@example.com');
    $i->waitForText('aaa@example.com');
    $i->searchFor('bbb@example.com');
    $i->waitForText('bbb@example.com');
    $i->searchFor('ccc@example.com');
    $i->waitForText('ccc@example.com');
    $i->searchFor('ddd@example.com');
    $i->waitForText('ddd@example.com');
    $i->searchFor('eee@example.com');
    $i->waitForText('eee@example.com');
    $i->searchFor('fff@example.com');
    $i->waitForText('fff@example.com');
    $i->searchFor('ggg@example.com');
    $i->waitForText('ggg@example.com');
    $i->searchFor('hhh@example.com');
    $i->waitForText('hhh@example.com');
    $i->searchFor('iii@example.com');
    $i->waitForText('iii@example.com');
    $i->seeNoJSErrors();
  }

  private function uploadCsvFile(\AcceptanceTester $i, $fileName = 'MailPoetImportList.csv') {
    $i->waitForText('Upload a file');
    $i->click('[data-automation-id="import-csv-method"]');
    $i->attachFile('[data-automation-id="import-file-upload-input"]', $fileName);
    $i->click('[data-automation-id="import-next-step"]');
  }

  private function chooseListAndConfirm(\AcceptanceTester $i) {
    $i->waitForText('Pick one or more list');
    // trigger dropdown to display selections
    $i->click('input.select2-search__field');
    // choose first list
    $i->click(['xpath' => '//*[@id="select2-mailpoet_segments_select-results"]/li[1]']);
    $i->click('.mailpoet_data_manipulation_step [data-automation-id="import-next-step"]');
    $i->waitForText('Import again');
  }

  private function proceedThroughClearout(\AcceptanceTester $i) {
    $proceedLinkText = 'Got it, I’ll proceed to import';
    $i->waitForText($proceedLinkText);
    $i->click($proceedLinkText);
  }
}
