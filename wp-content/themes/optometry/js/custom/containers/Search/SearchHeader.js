import React from "react";
import PropTypes from "prop-types";

const withHeader = Component => props => {

	return (
		<Component {...props} className={props.className} />
	)
};

const SearchHeader = ({ searchTerm, searchResults }) => {
	const headerComponent = withHeader( SearchHeader );
};

SearchHeader.propTypes = {
	searchTerm: PropTypes.string,
	searchResults: PropTypes.array
};