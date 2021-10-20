import React from "react";
import { Helmet } from "react-helmet";

export function HTML() {
	const helmet = Helmet.renderStatic();
	const bodyAttrs = helmet.bodyAttributes.toComponent();

	return (
		<body {...bodyAttrs}>

		</body>
	)
}