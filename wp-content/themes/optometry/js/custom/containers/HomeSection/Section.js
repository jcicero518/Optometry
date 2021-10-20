import React from 'react';
import PropTypes from 'prop-types';
import styled from 'styled-components';

import Caption from "./Caption";

const SectionStyle = styled.section`
  width: 100%;
  position: relative;
  background-repeat: no-repeat;
  background-image: ${ props => props.background ? `url(${props.background})` : "none" }
`;

const Section = (props) => {
  return (
    <SectionStyle background={props.sectionData.background.url} className={props.className}>
      <div className="wrap">
          <Caption {...props.sectionData} />
      </div>
    </SectionStyle>
  )
};

Section.propTypes = {
    className: PropTypes.string,
    sectionData: PropTypes.object
};

export default Section;

