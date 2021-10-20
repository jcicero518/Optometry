/* eslint-disable react/no-danger */
import React from 'react'
import PropTypes from 'prop-types';
import reactHtml from "../../util/innerHtml";

const Caption = (props) => {
    return (
        <div className="caption">
            <div className="caption-title">
                <h2 className="title">{props.title}</h2>
            </div>
            <div className="caption-desc" dangerouslySetInnerHTML={reactHtml(props.content)} />
        </div>
    )
}

export default Caption;