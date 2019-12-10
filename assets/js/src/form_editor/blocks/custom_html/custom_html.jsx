import MailPoet from 'mailpoet';
import icon from './icon.jsx';
import edit from './edit.jsx';

export const name = 'mailpoet-form/custom-html';

export const settings = {
  title: MailPoet.I18n.t('blockCustomHtml'),
  description: MailPoet.I18n.t('blockCustomHtmlDescription'),
  icon,
  category: 'fields',
  attributes: {
    content: {
      type: 'string',
      default: MailPoet.I18n.t('blockCustomHtmlDefault'),
    },
  },
  supports: {
    html: false,
    customClassName: false,
    multiple: true,
  },
  edit,
  save() {
    return null;
  },
};