import React, {Component} from "react";
import PropTypes from "prop-types";
import {TransitionGroup, CSSTransition} from "react-transition-group/TransitionGroup";
import Content from "./Content";
import reactHtml from "../../util/innerHtml";

class Widgets extends Component {

	render() {
		const {
			acf,
			leftNavMenuData,
			pageTemplate
		} = this.props;

		let widget, widgetType;

		widgetType = acf.hasOwnProperty( 'status' ) ? 'community' : 'nav';

		widgetType === 'community' ? widget = (
			<div>
				<div className="widget l-community u-cf">
					<div className="l-community-top">
						<div className="o-community-image">
							<img
								alt={acf.image_headshot.alt ? acf.image_headshot.alt : 'Headshot'}
								className="alignleft"
								src={acf.image_headshot.url} />
						</div>
						<div className="o-community-copy">
							<h3 className="widget-title">{acf.name}</h3>
							<p>{acf.status}</p>
							<h5>Hometown</h5>
							<p>{acf.hometown}</p>
							<h5>Alma Mater</h5>
							<p>{acf.alma_mater}</p>
						</div>
					</div>
					<div className="l-community-quote">
						<blockquote dangerouslySetInnerHTML={reactHtml(acf.testimonial_quote)} />
					</div>
				</div>
				<hr className="o-divider" />
				<div className="widget l-in-community">
					<h2 className="widget_title">{acf.section_title}</h2>
					<div className="o-section-image">
						<img alt={acf.section_image.alt !== '' ? acf.section_image.alt : 'In The Community'} src={acf.section_image.url} /></div>
					<div className="o-section-caption" dangerouslySetInnerHTML={Content.contentSetInnerHtml( acf.section_image_caption )} />
				</div>
			</div>
		) : widget = null;

		return widget;
	}
}

export default Widgets;