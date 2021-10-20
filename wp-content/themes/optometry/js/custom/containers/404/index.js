import React from "react";
import { Helmet } from "react-helmet";

import Header from "../Header";
import Footer from "../Footer";

const Page404 = () => {

	return (
		<div className="site-content site-wrapper">
			<Helmet>
				<title>Page not found | SUNY Optometry</title>
			</Helmet>
			<p>Page not found</p>
			<Footer id="colophon" className="site-footer" role="contentinfo" />
		</div>
	);
};

export default Page404;