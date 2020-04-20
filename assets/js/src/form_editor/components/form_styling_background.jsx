import React from 'react';
import PropTypes from 'prop-types';
import { useSelect } from '@wordpress/data';

const FormStylingBackground = ({ children }) => {
  const {
    fontColor,
    backgroundColor,
    fontSize,
    borderRadius,
    borderSize,
    borderColor,
    alignment,
    formPadding,
  } = useSelect(
    (select) => {
      const settings = select('mailpoet-form-editor').getFormSettings();

      return {
        backgroundColor: settings.backgroundColor,
        fontColor: settings.fontColor,
        fontSize: settings.fontSize,
        borderRadius: settings.borderRadius,
        borderSize: settings.borderSize,
        borderColor: settings.borderColor,
        alignment: settings.alignment,
        formPadding: settings.formPadding,
      };
    },
    []
  );

  let borderStyle;
  if (borderSize && borderColor) {
    borderStyle = 'solid';
  }

  let font;
  if (fontSize) font = Number(fontSize);
  let radius;
  if (borderRadius) radius = Number(borderRadius);
  let padding;
  if (formPadding) padding = Number(formPadding);
  let textAlign;
  if (alignment) {
    textAlign = alignment;
  }
  return (
    <div
      style={{
        backgroundColor,
        color: fontColor,
        fontSize: font,
        lineHeight: 1.2,
        borderRadius: radius,
        borderWidth: borderSize,
        borderColor,
        borderStyle,
        textAlign,
        padding,
      }}
    >
      {children}
    </div>
  );
};

FormStylingBackground.propTypes = {
  children: PropTypes.node.isRequired,
};

export default FormStylingBackground;
