/* eslint-disable react/no-danger */
import React from 'react';
import PropTypes from 'prop-types';
import LoadingIndicator from "../../components/LoadingIndicator";
import { createMuiTheme } from 'material-ui/styles';
//import blue from "material-ui/colors/blue";
import Button from "material-ui/Button";
import reactHtml from "../../util/innerHtml";

/*const primary = blue[500];
const theme = createMuiTheme({
	palette: {
		primary: blue
	}
});*/

const CaptionHero = (props) => {
    return (
        <div className="caption">
            <div className="wrap">
                <div className="caption-title">
                    <h2 className="title">{props.title ? props.title : <LoadingIndicator />}</h2>
                </div>
                <div className="caption-desc" dangerouslySetInnerHTML={props.content ? reactHtml(props.content) : null} />
	            <Button className="caption-mobile-more-button" href="/doctor-of-optometry-program" variant="flat" color="primary">Read More</Button>
            </div>
        </div>
    );
};

CaptionHero.propTypes = {
	content: PropTypes.oneOfType([
	    PropTypes.string,
        PropTypes.bool
    ]),
	title: PropTypes.oneOfType([
		PropTypes.string,
		PropTypes.bool
	])
};

export default CaptionHero;

