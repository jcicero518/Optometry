/* global OPTO_REST_API */
import React, {Component, PureComponent} from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import {TransitionGroup, CSSTransition} from "react-transition-group/TransitionGroup";

import Widgets from "./Widgets";
import WidgetMenu from "./WidgetMenu";
import CommunityTestimonial from "./CommunityTestimonial";
import HeroImage from "./HeroImage";
import Slider from "../../components/Slider";

import LoadingIndicator from "../../components/LoadingIndicator";
import { decodeEntity } from "../../util/entityDecoder";
import reactHtml from "../../util/innerHtml";
import {createStructuredSelector} from "reselect/es";
import {setSliderSlides} from "../../actions/globalActions";
import { selectSliderSlides } from "../../selectors";

class Content extends Component {

	static contentSetInnerHtml( content ) {
		return {
			__html: content
		}
	}

	static mapContentData( pageData ) {
		if ( ! pageData ) {
			return false;
		}

		return Object.assign( {}, pageData );
	}

	constructor(props) {
		super(props);
		this.sliderSlides = null;
	}

	componentDidMount() {
		const {
			pageData: { acf : { slides, display_slider } }
		} = this.props;

		if ( display_slider && display_slider === 'yes' ) {
			this.sliderSlides = slides.map( slide => slide.slide );
		}

	}

	render() {
		const {
			loading,
			leftNavMenuData,
			pageData,
			pageTemplate,
			mediaSizes
		} = this.props;

		const pageContent = Content.mapContentData( pageData );

		let page, heroImage, preloaderImage, secondary, secondaryNav, displaySlider, sliderSlides;

		if ( mediaSizes.full && mediaSizes.full.hasOwnProperty( 'source_url' ) ) {
			heroImage = mediaSizes.full.source_url;
			preloaderImage = mediaSizes.preloader.source_url;
		} else {
            heroImage = `${OPTO_REST_API.theme_dir}/dist/assets/images/hero/programhero.jpg`;
		}


		
		if ( pageTemplate !== false && pageTemplate.includes( 'community' ) ) {
			secondary = widget => (
				<aside id="secondary" className="widget-area secondary-navigation secondary-community" role="complementary">
					{widget}
				</aside>
			);
		} else {
			if ( leftNavMenuData ) {
				secondaryNav = widgetmenu => {

					const { leftNavMenuData } = widgetmenu.props;
					if ( widgetmenu && leftNavMenuData.length ) {
						return (
                            <nav id="secondary" className="widget-area secondary-navigation" role="navigation">
                                {widgetmenu}
                            </nav>
						)
					}
					return null;
                }
			} else {
				secondary = () => {
                    return (
                        <aside id="secondary" className="widget-area secondary-navigation" role="complementary">
                    		<LoadingIndicator />
						</aside>
					)
				};
				secondaryNav = () => {}
			}
		}

		if ( pageContent ) {
			displaySlider = pageData.acf.display_slider === 'yes';
			page = (
				<div id="content" className="site-content">
					{mediaSizes && !displaySlider ? <HeroImage mediaSizes={mediaSizes} srcPreload={preloaderImage} srcLoaded={heroImage} /> : null}
					<section className="hero-slider" style={{"width": "100%","position":"relative","clear":"both"}}>
						<Slider slides={this.sliderSlides} />
					</section>
					<div id="primary" className="content-area">
						<div className="wrap">
							<main id="main" className="site-main" role="main">
                                {loading ? <LoadingIndicator /> : null}
								<article className="page type-page hentry">
									<header className="entry-header">
										<h1 className="entry-title">{decodeEntity(pageData.title.rendered)}</h1>
									</header>
									<div className="entry-content" dangerouslySetInnerHTML={reactHtml( pageData.content.rendered )} />
								</article>
								{pageData.acf ? <CommunityTestimonial acf={pageData.acf} /> : null}
							</main>

							{secondary ? secondary( <Widgets acf={pageData.acf} {...this.props} /> ) : null}
							{secondaryNav ? secondaryNav( <WidgetMenu leftNavMenuData={leftNavMenuData} {...this.props} /> ) : null}
						</div>
					</div>
				</div>
			);
		}

		return page ? page : '';
	}
}

function mapDispatchToProps( dispatch ) {
	return {
		setSlider: ( slides ) => dispatch( setSliderSlides( slides ) )
	};
}

const mapStateToProps = createStructuredSelector({
	getSlider: selectSliderSlides()
});

/**
 * {mediaSizes ? <section className="site-top-hero section-row" style={{"backgroundImage":`url(${heroImage})`}} /> : null}
 */

//export default Content;
export default connect( mapStateToProps, mapDispatchToProps )( Content );
