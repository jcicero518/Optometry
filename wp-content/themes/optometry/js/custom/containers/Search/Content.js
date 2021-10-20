import React, {Component} from "react";
import { Link } from 'react-router-dom';
import LoadingIndicator from "../../components/LoadingIndicator";
import { decodeEntity } from "../../util/entityDecoder";
import reactHtml from "../../util/innerHtml";

class Content extends Component {

	static headerContent( props ) {
	    let header;

	    if ( ! props ) {
	        return false;
        }

        props.searchResults.length ?
            header = (
                <header className="entry-header">
                    <h1 className="entry-title">Search results for: {props.searchTerm}</h1>
                </header>
            ) :
            header = (
                <header className="entry-header">
                    <h1 className="entry-title">Nothing found for: {props.searchTerm}</h1>
                </header>
            );

	    return !props.loading ? header : null;
    }

    static resultsContent( props ) {
	    let results;

	    if ( ! props ) {
	        return false;
        }

        props.searchResults.length ?
            results = props.searchResults.map( result => (
                    <article key={result.id}>
                        <header className="entry-header">
                            <h2 className="entry-title">
                                <Link to={result.link.replace(`${location.origin}/`, '/')} rel="bookmark">{decodeEntity(result.title.rendered)}</Link>
                            </h2>
                        </header>
                        <div className="entry-summary" dangerouslySetInnerHTML={reactHtml(result.excerpt.rendered)} />
                        <footer className="entry-footer"> </footer>
                    </article>
                )) :
            results = (
                <p>Sorry, no results were found. Please try a different term.</p>
            );

        return !props.loading ? results : null;
    }

    render() {
        const { searchResults, searchTerm, loading } = this.props;

        return (

                <div id="primary" className="content-area">
                    <div className="wrap">
                        <main id="main" className="site-main" role="main">
	                        <a className="screen-reader-text" name="content" id="content" />
                            {loading ? <LoadingIndicator/> : null}
                            <div className="page type-search hentry">
                                {Content.headerContent(this.props)}
                                <div className="entry-content page-content">
                                    {Content.resultsContent(this.props)}
                                </div>
                            </div>
                        </main>
                    </div>
                </div>

        )
    }
}

export default Content;