import React, { Component } from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import { createStructuredSelector } from "reselect/es";

import {
	selectMobileNavStatus,
    makeSelectLocation,
    makeSelectLoading,
    makeSelectNavMenu,
	selectSliderSlides
} from "../../selectors";

import {
	makeSelectFrontData,
	makeSelectFrontId,
	makeSelectFrontSectionOne,
	makeSelectFrontSectionTwo,
	makeSelectFrontSectionThree
} from "./selector";

import { setHomeData } from "./actions";
import { setSliderSlides } from "../../actions/globalActions";

import { Helmet } from "react-helmet";

import Header from "../Header";
import Slider from "../../components/Slider";
import CaptionHero from "../HomeSection/CaptionHero";
import Footer from "../Footer";
import SectionHero from "../HomeSection/SectionHero";
import SectionLetter from "../HomeSection/SectionLetter";
import Section from "../HomeSection/Section";

class Home extends Component {

	static setWpAdminBar( pId ) {
		if ( ! pId ) {
			return false;
		}

		try {
			let editButton = document.getElementById('wp-admin-bar-edit');
			let editLink = editButton.hasChildNodes() ? editButton.children.item(0) : null;

			let updatedLink = editLink.href.replace(/post=(\d+)/, `post=${pId}`);

			editLink.setAttribute( 'href', updatedLink );
		} catch ( e ) {
			console.warn( `Not logged in.. ${e.message}` );
		}
	}

	constructor( props ) {
		super( props );
		this.sliderSlides = null;
		this.displaySlider = false;
	}

	componentWillMount() {
		this.props.getHomeSections();
	}

	componentDidMount() {
		const { frontPageId } = this.props;
		Home.setWpAdminBar( frontPageId );
	}


	render() {
		const {
			frontSectionsData: { display_slider, slides },
			mobileNavStatus,
			navMenuData,
			history
		} = this.props;


		if ( display_slider && display_slider === 'yes' ) {
			this.displaySlider = true;
			this.sliderSlides = slides.map( slide => slide.slide );
		}

		return (
			<div className="site-content site-wrapper">
				<Helmet>
					<body className="home" />
				</Helmet>
				<Header menuData={navMenuData} history={history} mobileNavStatus={mobileNavStatus} />
				{!this.displaySlider
					?
					<SectionHero className="site-top-hero section-row" sectionData={this.props.sectionOne} {...this.props} />
					:
					<section className="site-top-slider hero-slider section-row">
						<Slider slides={this.sliderSlides} overrides={{dots: false}} />
						<CaptionHero {...this.props.sectionOne} />
					</section>
				}
				<a className="screen-reader-text" name="content" id="content" />
				<Section className="site-optometrist section-row section-row-desk" sectionData={this.props.sectionTwo} />
				<Section className="site-optometrist section-row section-row-mobile" sectionData={this.props.sectionTwo} />
				<SectionLetter className="site-president section-row" sectionData={this.props.sectionThree} />
                <Footer id="colophon" className="site-footer" role="contentinfo" />
			</div>
		);
	}
}

function mapDispatchToProps(dispatch) {
	return {
		getHomeSections: () => dispatch( setHomeData( OPTO_REST_API.front_page_id ) ),
		setSlider: ( slides ) => dispatch( setSliderSlides( slides ) )
	};
}

const mapStateToProps = createStructuredSelector({
	location: makeSelectLocation(),
	loading: makeSelectLoading(),
	getSlider: selectSliderSlides(),
    mobileNavStatus: selectMobileNavStatus(),
    navMenuData: makeSelectNavMenu(),
	frontPageId: makeSelectFrontId(),
	frontSectionsData: makeSelectFrontData(),
	sectionOne: makeSelectFrontSectionOne(),
	sectionTwo: makeSelectFrontSectionTwo(),
	sectionThree: makeSelectFrontSectionThree()
});

Home.propTypes = {
	getHomeSections: PropTypes.func,
	history: PropTypes.object,
	mobileNavStatus: PropTypes.bool,
	navMenuData: PropTypes.oneOfType([
		PropTypes.array,
		PropTypes.bool
	]),
	frontPageId: PropTypes.string,
	frontSectionsData: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.object
	]),
	sectionOne: PropTypes.object,
	sectionTwo: PropTypes.object,
	sectionThree: PropTypes.object
};

export default connect( mapStateToProps, mapDispatchToProps )( Home );
