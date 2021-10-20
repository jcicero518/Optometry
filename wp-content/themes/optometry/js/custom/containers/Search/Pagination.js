import React, {Component} from "react";
import {
	Link
} from "react-router-dom";

class Pagination extends Component {

	constructor( props ) {
		super( props );
	}

	componentDidMount() {
		const {
			headers,
			currPage
		} = this.props;

		const { totalPages, totalResults } = headers;
	}

	handlePaginationClick( currPage ) {
		const {
			term,
			siteSearch
		} = this.props;

		scroll(0, 0);
		siteSearch( term, currPage );
	}


	buildPagination( currPage, numPages, term ) {
		const arrowLeft = (
			<span className="fa fa-chevron-circle-left" aria-hidden>
				<span className="screen-reader-text">Prev Page</span>
			</span>
		);
		const arrowRight = (
			<span className="fa fa-chevron-circle-right" aria-hidden>
				<span className="screen-reader-text">Next Page</span>
			</span>
		);
		const prevText = `Previous`;
		const nextText = `Next`;

		let prevLink = {
			link: <span>{prevText}</span>,
			enabled: false
		};

		let nextLink = {
			link: <Link to={`/search/${term}/` + ( currPage + 1)} onClick={() => this.handlePaginationClick(currPage + 1)}>{nextText}  {arrowRight}</Link>,
			enabled: true
		};

		if ( currPage > 1 && ( currPage < numPages ) ) {
			prevLink.link = <Link to={`/search/${term}/` + (currPage - 1)} onClick={() => this.handlePaginationClick(currPage - 1)}>{arrowLeft}  {prevText}</Link>;
			prevLink.enabled = true;
		} else if ( currPage === numPages ) {
			nextLink.link = <span>{nextText}</span>;
			nextLink.enabled = false;

			prevLink.link = <Link to={`/search/${term}/` + (currPage - 1)} onClick={() => this.handlePaginationClick(currPage - 1)}>{arrowLeft}  {prevText}</Link>;
			prevLink.enabled = true;
		}

		return (
			<nav className="l-pagination">
				<ul className="pager">
					{[prevLink, nextLink].map((link, index) =>
						<li key={index} className={link.enabled ? "" : "disabled"}>
							{link.link}
						</li>
					)}
				</ul>
			</nav>
		);

	}

	render() {
		const {
			headers,
			currPage,
			term
		} = this.props;

		const { totalPages, totalResults } = headers;

		return (
			<div className="l-pagination-wrapper">
				<div className="wrap">
					<hr />
					{totalResults > 0 ? <p>Displaying page {currPage} of {totalPages}</p> : null}
					{totalPages > 1 ? this.buildPagination( currPage, totalPages, term ) : null}
				</div>
			</div>
		)
	}
}

export default Pagination;