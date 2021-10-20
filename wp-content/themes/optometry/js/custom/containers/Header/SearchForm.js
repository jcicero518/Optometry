import React, { Component } from "react";
import { connect } from "react-redux";
import {createStructuredSelector} from "reselect";
import * as actions from "../../actions/globalActions";
import * as selectors from "../../selectors";

class SearchForm extends Component {

    constructor(props) {
        super(props);
        this.searchFormSubmit = this.searchFormSubmit.bind( this );
        this.searchChange = this.searchChange.bind( this );
        this.focusInput = this.focusInput.bind( this );
    }

    searchFormSubmit( event ) {
        event.preventDefault();
	    const { history } = this.props;

	    if ( 0 < this.searchInput.value.length ) {
	    	history.push( `/search/${this.searchInput.value}` );
	    }
    }

    searchChange( event ) {
        event.preventDefault();
        const searchTerm = event.target.value;
    }

    focusInput() {
        this.searchInput.focus();
    }


    render() {
        return (

                <form onSubmit={this.searchFormSubmit} role="search" className="search-form">
                    <label>
                        <span className="screen-reader-text">Search for:</span>
                        <input
                            type="search"
                            className="search-field"
                            placeholder="Search"
                            defaultValue=""
                            name="s"
                            ref={(input) => this.searchInput = input}
                            onClick={this.focusInput}
                            onChange={this.searchChange}
                        />
                    </label>
                    <button type="submit" className="search-submit" value="Search">
                        <span className="fa fa-search" aria-hidden="true">
                            <span className="screen-reader-text">Search</span>
                        </span>
                    </button>
                </form>
        );
    }
}

const mapStateToProps = createStructuredSelector({
    location: selectors.makeSelectLocation(),
    searchTerm: selectors.makeSelectSearchTerm(),
    searchResults: selectors.makeSelectSearchResults()
});

function mapDispatchToProps( dispatch ) {
    return {
        siteSearch: ( term ) => dispatch( actions.searchSite( term ) )
    }
}

export default connect( mapStateToProps, mapDispatchToProps )( SearchForm );