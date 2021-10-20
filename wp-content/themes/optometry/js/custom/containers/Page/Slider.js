import React from "react";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.scss";

const SlickSlider = ( props ) => {
	const { slides } = props;
	slides.map( s => {
		console.log(s, 's');
	});
	return (
		<div className="slick-slider">
			{slides.map( (key, slide) => (
				<img key={slide.ID} alt={slide.alt} src={slide.url} />
			))}
		</div>
	);

};

export default SlickSlider;