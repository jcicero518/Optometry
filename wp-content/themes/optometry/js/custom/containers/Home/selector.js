/**
 * The home state selectors
 */

import { createSelector } from 'reselect/es';

const selectHome = state => state.get( 'home' );

const makeSelectFrontId = () => createSelector(
	selectHome,
	(homeState) => homeState.getIn( ['homeData', 'postId'] )
);

const makeSelectAcf = () => createSelector(
	selectHome,
	(homeState) => homeState.getIn( ['homeData', 'acf'] )
);

const selectSliderSettings = () => createSelector(
	selectHome,
	(homeState) => homeState.get( 'sliderSettings' )
);

const makeSelectFrontData = () => createSelector(
	selectHome,
	(homeState) => homeState.getIn( ['homeData', 'sectionsData'] )
);

const makeSelectFrontSectionOne = () => createSelector(
	selectHome,
	(homeState) => {
		return {
			title: homeState.getIn( ['homeData', 'sectionOneTitle'] ),
			content: homeState.getIn( ['homeData', 'sectionOneContent'] ),
			background: homeState.getIn( ['homeData', 'sectionOneImage'] )
		};
	}
);

const makeSelectFrontSectionTwo = () => createSelector(
	selectHome,
	(homeState) => {
		return {
			title: homeState.getIn( ['homeData', 'sectionTwoTitle'] ),
			content: homeState.getIn( ['homeData', 'sectionTwoContent'] ),
			background: homeState.getIn( ['homeData', 'sectionTwoImage'] )
		};
	}
);

const makeSelectFrontSectionThree = () => createSelector(
	selectHome,
	(homeState) => {
		return {
			title: homeState.getIn( ['homeData', 'sectionThreeTitle'] ),
			content: homeState.getIn( ['homeData', 'sectionThreeContent'] ),
			image: homeState.getIn( ['homeData', 'sectionThreeImage'] )
		};
	}
);

export {
	selectHome,
	makeSelectAcf,
	selectSliderSettings,
	makeSelectFrontId,
	makeSelectFrontData,
	makeSelectFrontSectionOne,
	makeSelectFrontSectionTwo,
	makeSelectFrontSectionThree
};