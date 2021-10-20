/* global OPTO_FOOTER_MENU */
import React from 'react';
import PropTypes from 'prop-types';
import styled from 'styled-components';
import {TransitionGroup, CSSTransitionGroup} from "react-transition-group";
import reactHtml from "../../util/innerHtml";

const FooterStyle = styled.footer``;

const Footer = (props) => {
    return (
	    <CSSTransitionGroup component="div"
	                        className="footer-wrapper wrap-transition"
	                        transitionName="fade"
	                        transitionAppear={true}
	                        transitionAppearTimeout={500}
	                        transitionEnterTimeout={500}
	                        transitionLeaveTimeout={1000}>
            <FooterStyle id={props.id} className={props.className} role={props.role}>
                <div className="wrap">
                    <div className="footer-cols">
                        <div className="footer-item">
                            <div className="footer-navigation">
                                <div className="nav-menu" dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.menu_col_one)} />
                                <div dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.menu_col_two)} />
                            </div>
                        </div>
                        <div className="footer-item">
                            <div className="footer-navigation">
                                <div className="nav-menu" dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.menu_col2_one)} />
                                <div className="nav-menu" dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.menu_col2_two)} />
                                <div dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.menu_col2_three)} />
                            </div>
                        </div>
                        <div className="footer-item last">
                            <div className="address-info" dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.address_info)} />
                            <div className="site-info" dangerouslySetInnerHTML={reactHtml(OPTO_FOOTER_MENU.site_info)} />
                        </div>
                    </div>
                </div>
            </FooterStyle>
        </CSSTransitionGroup>
    )
}

export default Footer;