import React from 'react';
import PropTypes from 'prop-types';
import styled from 'styled-components';

import CaptionLetter from "./CaptionLetter";

const SectionStyle = styled.section`
  width: 100%;
  position: relative
`;

const SectionLetter = (props) => {
  return (
    <SectionStyle className={props.className}>
      <div className="wrap">
          <CaptionLetter {...props.sectionData} />
      </div>
    </SectionStyle>
  )
};

SectionLetter.propTypes = {
    className: PropTypes.string,
    sectionData: PropTypes.object
};

export default SectionLetter;

