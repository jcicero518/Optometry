/**
 * The global state selectors
 */

import { createSelector } from 'reselect/es';

const selectGlobal = (state) => state.get('global');
const selectRoute = (state) => state.get('route');

const selectSwipeDelta = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['swiping', 'deltaX'] )
);

const selectSwipeFinal = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['swiping', 'finalX'] )
);

const selectCurrentPostId = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'postId' )
);

const selectSliderSlides = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['slider', 'slides'] )
);

const selectMobileNavStatus = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['mobileNav', 'isOpen'] )
);

const selectButtonDrawer = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['buttonDrawer', 'isOpen'] )
);

const selectPageChildren = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['pageChildren', 'children'] )
);



const makeSelectLeftNavMenu = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'leftNavData' )
);

const makeSelectNavMenu = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['menuData', 'navObjects'] )
);

const makeSelectSearchTerm = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['search', 'term'] )
);

const makeSelectSearchResults = () => createSelector(
    selectGlobal,
    (globalState) => globalState.getIn( ['search', 'results'] )
);

const makeSelectSearchHeaders = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['search', 'headers'] )
);

const makeSelectSearchCurrPage = () => createSelector(
	selectGlobal,
	(globalState) => globalState.getIn( ['search', 'currPage'] )
);

const makeSelectPage = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'pageData' )
);

const makeSelectPageTemplate = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'pageTemplate' )
);

const makeSelectMedia = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'mediaData' )
);

const makeSelectMediaId = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'mediaId' )
);

const makeSelectMediaSizes = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get( 'mediaSizes' )
);

const makeSelectLoading = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get('loading')
);

const makeSelectError = () => createSelector(
	selectGlobal,
	(globalState) => globalState.get('error')
);

const makeSelectLocation = () => createSelector(
	selectRoute,
	(routeState) => routeState.get('location').toJS()
);

export {
	selectGlobal,
	selectCurrentPostId,
	selectSwipeDelta,
	selectSwipeFinal,
	selectSliderSlides,
	selectMobileNavStatus,
	selectButtonDrawer,
	selectPageChildren,
	makeSelectLeftNavMenu,
	makeSelectNavMenu,
	makeSelectPage,
	makeSelectPageTemplate,
	makeSelectMedia,
	makeSelectMediaId,
	makeSelectMediaSizes,
	makeSelectLoading,
	makeSelectError,
	makeSelectLocation,
	makeSelectSearchTerm,
	makeSelectSearchResults,
	makeSelectSearchHeaders,
	makeSelectSearchCurrPage
};
