import React from "react";

import { Transition, CSSTransitionGroup } from "react-transition-group";

export default function LoadingItem( img ) {
	return (
		<CSSTransitionGroup
			transitionName="loadingItem"
			transitionAppear
			transitionAppearTimeout={500}
			transitionEnterTimeout={500}
			transitionLeaveTimeout={300}>
			<img className="feed__loading-item" src={img} />
		</CSSTransitionGroup>
	)
}