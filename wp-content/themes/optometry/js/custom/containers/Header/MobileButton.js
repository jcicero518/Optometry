import React, {Component} from 'react';
import PropTypes from 'prop-types';
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { setMobileNavStatus } from "../../actions/globalActions";
import {selectMobileNavStatus} from "../../selectors";

class MobileButton extends Component {

    constructor( props ) {
        super( props );

        this.onButtonClick = this.onButtonClick.bind( this );
    }

    onButtonClick( event ) {
        event.preventDefault();
        this.props.setMobileNav();
    }

    render() {
        const { mobileNavStatus } = this.props;

        return (
            <div className="mobile-button-container">
                <button
                    onClick={this.onButtonClick}
                    id="button-trigger-menu"
                    className="menu-toggle"
                    type="button"
                    aria-controls="primary-menu"
                    aria-expanded={mobileNavStatus}>
                    <span className="screen-reader-text">Menu</span>
                    <span className="icon-bar" />
                    <span className="icon-bar" />
                    <span className="icon-bar" />
                </button>
            </div>
        );
    }
}

const mapStateToProps = createStructuredSelector({
    mobileNavStatus: selectMobileNavStatus()
});

function mapDispatchToProps( dispatch ) {
    return {
        setMobileNav: ( isOpen ) => dispatch( setMobileNavStatus( isOpen ) )
    }
}

export default connect( mapStateToProps, mapDispatchToProps )( MobileButton );
