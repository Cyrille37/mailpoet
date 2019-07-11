import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';
import PropTypes from 'prop-types';
import ReactStringReplace from 'react-string-replace';
import MailPoet from 'mailpoet';
import PreviousNextStepButtons from './previous_next_step_buttons.jsx';

const ServicesMessage = () => {
  let message = ReactStringReplace(MailPoet.I18n.t('useServices'), '%1$s', () => (
    <a
      href="https://www.briteverify.com"
      target="_blank"
      rel="noreferrer noopener"
      key="BriteVerify"
    >
      BriteVerify
    </a>
  ));
  message = ReactStringReplace(message, '%2$s', () => (
    <a
      href="https://www.datavalidation.com/"
      target="_blank"
      rel="noreferrer noopener"
      key="DataValidation"
    >
      DataValidation
    </a>
  ));
  message = ReactStringReplace(message, '%3$s', () => (
    <a
      href="https://neverbounce.com/"
      target="_blank"
      rel="noreferrer noopener"
      key="NeverBounce"
    >
      NeverBounce
    </a>
  ));
  return message;
};

class StepInputValidation extends Component {
  constructor(props) {
    super(props);
    this.state = {
      subscribersAgreed: false,
      sentOnceLastYear: false,
      understand: false,
    };
  }

  componentDidMount() {
    if (typeof (this.props.stepMethodSelectionData) === 'undefined') {
      this.props.history.replace('step_method_selection');
    }
  }

  isFormValid() {
    return this.state.subscribersAgreed
      && this.state.sentOnceLastYear
      && this.state.understand;
  }

  render() {
    return (
      <div className="mailpoet_import_validation_step">
        <label htmlFor="subscribers_agreed">
          <input
            type="checkbox"
            checked={this.state.subscribersAgreed}
            onChange={e => this.setState({ subscribersAgreed: e.target.checked })}
            name="subscribers_agreed"
            id="subscribers_agreed"
            data-automation-id="subscribers_agreed"
          />
          {MailPoet.I18n.t('subscribersAgreed')}
        </label>
        <p className="description">
          {MailPoet.I18n.t('dontEmailSubscribers')}
          {' '}
          <a
            href="https://kb.mailpoet.com/article/127-checklist-before-importing-subscribers"
            target="_blank"
            rel="noreferrer noopener"
          >
            {MailPoet.I18n.t('readSupportArticle')}
          </a>
        </p>
        <label htmlFor="sent_once_last_year">
          <input
            type="checkbox"
            checked={this.state.sentOnceLastYear}
            onChange={e => this.setState({ sentOnceLastYear: e.target.checked })}
            name="sent_once_last_year"
            id="sent_once_last_year"
            data-automation-id="sent_once_last_year"
          />
          {MailPoet.I18n.t('sentOnceYear')}
        </label>
        <p className="description">
          {MailPoet.I18n.t('emailAddressesWillBounce')}
        </p>
        <p className="description">
          <ServicesMessage />
        </p>
        <label htmlFor="understand">
          <input
            type="checkbox"
            checked={this.state.understand}
            onChange={e => this.setState({ understand: e.target.checked })}
            name="understand"
            id="understand"
            data-automation-id="input_understand"
          />
          {MailPoet.I18n.t('youUnderstand')}
        </label>
        <p className="description">
          {MailPoet.I18n.t('weWillSuspend')}
        </p>
        <PreviousNextStepButtons
          canGoNext={this.isFormValid()}
          onPreviousAction={() => this.props.history.push('step_method_selection')}
          onNextAction={() => this.props.history.push('step_data_manipulation')}
        />
      </div>
    );
  }
}

StepInputValidation.propTypes = {
  history: PropTypes.shape({
    push: PropTypes.func.isRequired,
    replace: PropTypes.func.isRequired,
  }).isRequired,
  stepMethodSelectionData: PropTypes.shape({
    duplicate: PropTypes.arrayOf(PropTypes.string),
    header: PropTypes.arrayOf(PropTypes.string),
    invalid: PropTypes.arrayOf(PropTypes.string),
    role: PropTypes.arrayOf(PropTypes.string),
    subscribersCount: PropTypes.number,
    subscribers: PropTypes.arrayOf(PropTypes.arrayOf(PropTypes.string)),
  }),
};

StepInputValidation.defaultProps = {
  stepMethodSelectionData: undefined,
};

export default withRouter(StepInputValidation);
