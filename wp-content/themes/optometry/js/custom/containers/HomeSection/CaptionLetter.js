/* eslint-disable react/no-danger */
import React from 'react'
import PropTypes from 'prop-types';
import Fade from "../../components/Image/Fade";
import reactHtml from "../../util/innerHtml";

const CaptionLetter = (props) => {
    return (
        <div className="section-letter">
            <div className="caption-title">
                <h2 className="title">{props.title}</h2>
            </div>
            <aside className="section-image">
                <Fade height={props.image ? props.image.height : 300}>
                    <img alt="Letter from the President" src={props.image ? props.image.url : null} />
                </Fade>
            </aside>
            <div className="caption">
                <div className="caption-desc" dangerouslySetInnerHTML={reactHtml(props.content)} />
            </div>
        </div>
    )
}

export default CaptionLetter;