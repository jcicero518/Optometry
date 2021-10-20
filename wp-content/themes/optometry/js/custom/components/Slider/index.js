import React, { Component } from "react";
import PropTypes from "prop-types";
import SlickSlider from "react-slick";

import Fade from "../Image/Fade";

import "slick-carousel/slick/slick.scss";
import "slick-carousel/slick/slick-theme.scss";

class Slider extends Component {

	render() {
		const { slides } = this.props;
		const overrides = this.props.overrides ? this.props.overrides : {};

		const defaultSettings = {
			dots: true,
			fade: true,
			lazyLoad: 'ondemand',
			arrows: true,
			infinite: true,
			speed: 500,
			autoplay: true,
			autoplaySpeed: 5000,
			slidesToShow: 1,
			slidesToScroll: 1
		};

		const settings = Object.assign( {}, defaultSettings, overrides );

		let slider = (
				<SlickSlider {...settings}>
					{slides ? slides.map( s => (
						<img key={s.ID} alt={s.alt ? s.alt : 'Slider Image'} src={s.url} />
					)) : null}
				</SlickSlider>
		);

		return slides ? slider : null;
	}

}

Slider.propTypes = {
	overrides: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.object
	]),
	slides: PropTypes.array
};

export default Slider;