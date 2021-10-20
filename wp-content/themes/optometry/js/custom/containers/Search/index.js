import React, { Component } from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";

import { createStructuredSelector } from "reselect/es";
import * as selectors from "../../selectors/index";
import * as actions from "../../actions/globalActions";

import { Helmet } from "react-helmet";

import Header from "../Header";
import Content from "./Content";
import Pagination from "./Pagination";
import Footer from "../Footer";

class Search extends Component {

	componentWillMount() {
		const { term, page } = this.props.match.params;
		let currPage = page ? page : 1;
		this.props.siteSearch( term, currPage );
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.match.params.term !== this.props.match.params.term ) {
			this.updated();
		}
	}

	updated() {
		const { term, page } = this.props.match.params;
		let currPage = page ? page : 1;
		this.props.siteSearch( term, page );
	}

	render() {
		const {
			mobileNavStatus,
			navMenuData,
			history,
			searchHeaders,
			currPage
		} = this.props;

		return (
            <div className="site-content">
				<Helmet>
					<title>{this.props.match.params.term ? `Search Results for "${this.props.match.params.term}"` : 'Search Results'}</title>
					<body className="search search-results" />
				</Helmet>
				<Header menuData={navMenuData} history={history} mobileNavStatus={mobileNavStatus} />
				<Content {...this.props} />
	            <Pagination
		            headers={searchHeaders}
		            currPage={parseInt(currPage)}
		            term={this.props.match.params.term}
		            siteSearch={this.props.siteSearch} />
                <Footer id="colophon" className="site-footer" role="contentinfo" />
			</div>
		);
	}
}

const mapStateToProps = createStructuredSelector({
    location: selectors.makeSelectLocation(),
    loading: selectors.makeSelectLoading(),
    mobileNavStatus: selectors.selectMobileNavStatus(),
    leftNavMenuData: selectors.makeSelectLeftNavMenu(),
    navMenuData: selectors.makeSelectNavMenu(),
	searchTerm: selectors.makeSelectSearchTerm(),
	searchResults: selectors.makeSelectSearchResults(),
	searchHeaders: selectors.makeSelectSearchHeaders(),
	currPage: selectors.makeSelectSearchCurrPage()
});

function mapDispatchToProps( dispatch ) {
    return {
        getNavMenu: () => dispatch( actions.receivedNavMenuRequest( 'main-menu' ) ),
        getPage: ( slug ) => dispatch( actions.receivedPageRequest( slug ) ),
	    siteSearch: ( term, currPage = 1, numPages = 5 ) => dispatch( actions.searchSite( term, currPage, numPages ) ) // saga
    }
}

Search.propTypes = {
	history: PropTypes.object,
	match: PropTypes.object,
	mobileNavStatus: PropTypes.bool,
	navMenuData: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.array
	]),
	siteSearch: PropTypes.func
}

export default connect( mapStateToProps, mapDispatchToProps )( Search );