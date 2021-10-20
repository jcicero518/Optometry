import React, { Component } from "react";
import PropTypes from "prop-types";

class HeroImage extends Component {

	constructor( props ) {
		super( props );
		this.fullImageSrc = null;
	}

	componentDidMount() {
		const fullImageLoader = new Image();
		fullImageLoader.src = this.props.srcLoaded;

		fullImageLoader.onload = () => {
			this.fullImageSrc.setAttribute( 'style', `background-image: url(${this.props.srcLoaded})` );
			this.fullImageSrc.classList.add( 'iron-image-fade-in' );
		};
	}


	render() {
		const {
			mediaSizes,
			srcPreload,
			srcLoaded
		} = this.props;

		return (
			<section className="site-top-hero section-row iron-image-container">
				<div className="iron-image-loaded" ref={imageLoadedElem => this.fullImageSrc = imageLoadedElem} />
				<div className="iron-image-preload" style={{"backgroundImage":`url(${srcPreload})`}} />
			</section>
		);
	}
}

export default HeroImage;

HeroImage.propTypes = {
  mediaSizes: PropTypes.object,
  srcLoaded: PropTypes.string,
  srcPreload: PropTypes.string
}