
import { fromJS } from 'immutable';

const initialPageState = fromJS({
    pageData: false,
    pageTemplate: false,
    postId: false,
    mediaData: false,
    mediaId: false,
    mediaSizes: false
});

function pageReducer( state = initialPageState, action ) {

}