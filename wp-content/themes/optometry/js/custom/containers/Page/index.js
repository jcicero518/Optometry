import PropTypes from 'prop-types';
import React, { Component } from "react";
import { connect } from "react-redux";

import {
	selectCurrentPostId,
	selectMobileNavStatus,
	selectButtonDrawer,
	selectPageChildren,
	makeSelectLeftNavMenu,
	makeSelectNavMenu,
	makeSelectPage,
	makeSelectMedia,
	makeSelectMediaId,
	makeSelectMediaSizes,
	makeSelectLocation,
	makeSelectLoading,
	makeSelectPageTemplate,
	makeSelectError
} from "../../selectors/index";
import {
	setOpenDrawer,
	setPage,
	setPageChildren,
	receivedPageRequest
} from "../../actions/globalActions";
import { createStructuredSelector } from "reselect/es";

import { Helmet } from "react-helmet";

import LoadingIndicator from "../../components/LoadingIndicator";

import Header from "../Header";
import DrawerButton from "./DrawerButton";
import Content from "./Content";
import Footer from "../Footer";

class Page extends Component {

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
		this.onPageClick = this.onPageClick.bind( this );
	}

    onPageClick( event ) {
    	let cancelClose = event.target.parentNode.classList.contains( 'drawer-container' );
    	if ( ! cancelClose && this.props.buttonDrawerStatus ) {
    		this.props.setDrawer();
	    }
    }

	componentWillMount() {
		const { match } = this.props;
		const matchParams = match.params;

		let getSlug = matchParams.hasOwnProperty( 'parent' ) ? matchParams.parent : '';

		if ( matchParams.hasOwnProperty( 'page' ) ) {
			getSlug = matchParams.page;
		}
		if ( matchParams.hasOwnProperty( 'tertiary' ) ) {
			getSlug = matchParams.tertiary;
		}

		this.props.getPage( getSlug );
		//this.props.setPage( getSlug );
	}

    componentDidUpdate( prevProps ) {
		let {
			location: { pathname },
			pageData: { id }
		} = this.props;

		if ( prevProps.location.pathname !== pathname ) {
			this.routeChanged();
		}
		if ( prevProps.pageData.id !== id ) {
			this.pageIdChanged();
		}
	}

	pageIdChanged() {
    	let {
    		pageData: { id }
    	} = this.props;

    	Page.setWpAdminBar( id );
	}

	routeChanged() {
		const { match } = this.props;
		const matchParams = match.params;

		let getSlug = matchParams.hasOwnProperty( 'parent' ) ? matchParams.parent : '';

		if ( matchParams.hasOwnProperty( 'page' ) ) {
			getSlug = matchParams.page;
		}
		if ( matchParams.hasOwnProperty( 'tertiary' ) ) {
			getSlug = matchParams.tertiary;
		}

		this.props.getPage( getSlug );
	}

	render() {
		const {
			mobileNavStatus,
			buttonDrawerStatus,
			navMenuData,
			pageData,
			history,
			loading,
			pageError
		} = this.props;


		return (
			<div onClick={this.onPageClick} className="site-content site-wrapper">
					<Helmet>
						<title>{pageData.title ? `${pageData.title.rendered} | SUNY Optometry` : 'SUNY Optometry'}</title>
						<body className={pageData.template ? pageData.template : 'page-template-default page'} />
					</Helmet>
					<Header menuData={navMenuData} history={history} mobileNavStatus={mobileNavStatus} />
					<DrawerButton
						pageData={pageData}
						buttonDrawerStatus={buttonDrawerStatus}
						history={history}
						menuData={navMenuData} />
					{loading ? <LoadingIndicator /> : null}
					{pageData ? <Content {...this.props} /> : null}
					<Footer id="colophon" className="site-footer" role="contentinfo" />
			</div>
		);
	}
}

/**
 *
 * @param dispatch
 * @returns {{getPage: function(*=): *, setPage: function(*=): *}}
 */
function mapDispatchToProps( dispatch ) {

	return {
		getPage: ( slug ) => dispatch( receivedPageRequest( slug ) ),
		getChildren: ( postId ) => dispatch( setPageChildren( postId ) ),
		setDrawer: ( isOpen ) => dispatch( setOpenDrawer( isOpen ) ),
		setPage: ( slug ) => dispatch( setPage( slug ) )
	};
}

/**
 * The mapStateToProps function's first argument is the entire Redux store’s state and
 * it returns an object to be passed as props. It is often called a selector.
 * Use reselect to efficiently compose selectors and compute derived data.
 */
const mapStateToProps = createStructuredSelector({
	location: makeSelectLocation(),
	loading: makeSelectLoading(),
	pageError: makeSelectError(),
	currentPostId: selectCurrentPostId(),
	mobileNavStatus: selectMobileNavStatus(),
	buttonDrawerStatus: selectButtonDrawer(),
	currentChildren: selectPageChildren(),
	leftNavMenuData: makeSelectLeftNavMenu(),
	navMenuData: makeSelectNavMenu(),
	pageData: makeSelectPage(),
	pageTemplate: makeSelectPageTemplate(),
	mediaData: makeSelectMedia(),
	mediaId: makeSelectMediaId(),
	mediaSizes: makeSelectMediaSizes()
});

/**
 * Connects a component to a Redux store, facade around connectAdvanced
 * @param function - mapStateToProps - mapStateToProps(state, [ownProps]): stateProps
 *      When specified, the returned component class subscribes to Redux store updates. Anytime
 *      the store is updated, mapStateToProps is called. Pass null to unsubscribe from store updates
 *
 * ( Page ) is the wrapped component, let c = connect(.., ..)(Component) - c.WrappedComponent = Page(props) { }
 * @returns a new connected component class
 */

Page.propTypes = {
	currentPostId: PropTypes.oneOfType([
		PropTypes.number,
		PropTypes.bool
	]),
	getPage: PropTypes.func,
	history: PropTypes.object,
	loading: PropTypes.bool,
	match: PropTypes.object,
	mobileNavStatus: PropTypes.bool,
	navMenuData: PropTypes.oneOfType([
		PropTypes.array,
		PropTypes.bool
	]),
	pageData: PropTypes.oneOfType([
		PropTypes.object,
		PropTypes.bool
	])
};

/**
 * connect ultimately looks like this:
 *   // connect is a function that returns another function
 *   const enhance = connect(mapStateToProps, mapDispatchToProps);
 *   // The returned function is a HOC, which returns a component that is connected
 *   // to the Redux store
 *   const ConnectedPage = enhance(Page);
 */
export default connect( mapStateToProps, mapDispatchToProps )( Page );

