import { fromJS } from 'immutable';
import ld from "lodash";

import {
	SET_FRONT_PAGE_ID,
	SET_HOME_DATA,
	SET_HOME_DATA_SUCCESS,
	SET_HOME_DATA_ERROR
} from "../../actions/actionTypes";

const initialState = fromJS({
	homeData: {
		postId: OPTO_REST_API.front_page_id ? OPTO_REST_API.front_page_id : false,
		acf: false,
		sectionsData: false,
		sectionOne: false,
		sectionOneTitle: false,
		sectionOneContent: false,
		sectionOneImage: false,
		sectionOneLink: false,
		sectionTwoData: false,
		sectionTwoTitle: false,
		sectionTwoContent: false,
		sectionTwoImage: false,
		sectionTwoLink: false,
		sectionThree: false
	},
	sliderSettings: {}
});

function homeReducer( state = initialState, action ) {
	switch ( action.type ) {
		case SET_FRONT_PAGE_ID:
			return state.setIn( ['homeData', 'postId'], action.postId );
		case SET_HOME_DATA:
			return state.set( 'loading', true );
		case SET_HOME_DATA_SUCCESS:
			return state
				.set( 'loading', false )
				.setIn( ['homeData', 'acf'], action.data.acf )
				.setIn( ['homeData', 'sectionsData'], action.data.acf )
				.setIn( ['homeData', 'sectionOneTitle'], action.data.acf.hero_content.caption_title )
				.setIn( ['homeData', 'sectionOneContent'], action.data.acf.hero_content.caption_content)
				.setIn( ['homeData', 'sectionOneImage'], action.data.acf.hero_content.background_image )
				.setIn( ['homeData', 'sectionTwoTitle'], action.data.acf.section_two.section_title )
				.setIn( ['homeData', 'sectionTwoContent'], action.data.acf.section_two.section_content)
				.setIn( ['homeData', 'sectionTwoImage'], action.data.acf.section_two.background_image )
				.setIn( ['homeData', 'sectionThreeTitle'], action.data.acf.section_three.section_title )
				.setIn( ['homeData', 'sectionThreeContent'], action.data.acf.section_three.section_content)
				.setIn( ['homeData', 'sectionThreeImage'], action.data.acf.section_three.section_image );
		default:
			return state;
	}
}

export default homeReducer;