import MailPoet from 'mailpoet';
import _ from 'underscore';

const indexedCf = {};

function getCustomField(formItems) {
  if (!formItems.customfield_id) return Promise.resolve();

  if (indexedCf[formItems.customfield_id] !== undefined) {
    return Promise.resolve(indexedCf[formItems.customfield_id]);
  }
  window.mailpoet_custom_fields.forEach((cf) => {
    indexedCf[cf.id] = cf;
  });
  return Promise.resolve(indexedCf[formItems.customfield_id]);
}

const customfieldField = {
  name: 'customfield_id',
  type: 'selection',
  endpoint: 'custom_fields',
  resetSelect2OnUpdate: true,
  placeholder: MailPoet.I18n.t('selectCustomfields'),
  forceSelect2: true,
  getLabel: _.property('name'),
  getValue: _.property('id'),
};

const customfieldSelectField = {
  name: 'customfield_value',
  type: 'selection',
  resetSelect2OnUpdate: true,
  placeholder: MailPoet.I18n.t('selectCustomfieldValues'),
  forceSelect2: true,
  getLabel: _.property('name'),
  getValue: _.property('id'),
  values: [],
};

export default (formItems) => getCustomField(formItems).then((customField) => {
  const formFields = [customfieldField];

  if (!customField) return Promise.resolve(formFields);

  switch (customField.type) {
    case 'checkbox':
      break;
    case 'select':
      customField.params.values.forEach((item, idx) => {
        customfieldSelectField.values.push({ id: idx, name: item.value });
      });
      formFields.push(customfieldSelectField);
      break;
    default:
  }
  return Promise.resolve(formFields);
});
