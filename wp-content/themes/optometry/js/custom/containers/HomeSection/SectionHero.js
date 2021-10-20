import React from 'react';
import PropTypes from 'prop-types';
import styled from 'styled-components';

import CaptionHero from "./CaptionHero";

const SectionStyle = styled.section`
  width: 100%;
  position: relative;
  background-repeat: no-repeat;
  background-image: ${ props => props.background ? `url(${props.background})` : "none" } !important
`;

const SectionHero = (props) => {
  return (
    <SectionStyle background={props.sectionData.background.url} className={props.className}>
        <div className="top-hero-image" />
        <CaptionHero {...props.sectionData} />
    </SectionStyle>
  )
};

SectionHero.propTypes = {
    className: PropTypes.string,
    sectionData: PropTypes.object
};

export default SectionHero;

