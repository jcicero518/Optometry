import React from "react";
import PropTypes from "prop-types";
import { Link } from "react-router-dom";
import SearchForm from "./SearchForm";
import MobileButton from "./MobileButton";

const Masthead = ( {history} ) => {

	return (
		<header id="masthead" className="site-header" role="banner">

			<div className="wrap">
				<div className="site-branding">
					<h1 className="site-title site-logo">
						<Link to="/" rel="home">
							<img alt="SUNY Optometry" src={`${OPTO_REST_API.theme_dir}/assets/images/optologo.png`} />
						</Link>
					</h1>
				</div>

				<div className="site-actions">
					<div className="site-search-form">
						<SearchForm history={history} />
					</div>
					<MobileButton />
				</div>
			</div>

		</header>
	)
};

export default Masthead;