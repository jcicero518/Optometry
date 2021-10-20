/* global OPTO_REST_API */
import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import styled from "styled-components";
import reactHtml from "../../util/innerHtml";

class CommunityTestimonial extends Component {

    static fieldAlmaMater( field ) {
        return field ? (
            <Fragment>
                <h5>Alma Mater</h5>
                <p>{field}</p>
            </Fragment>
        ) : null;
    }

    static fieldHomeTown( field ) {
        return field ? (
            <Fragment>
                <h5>Hometown</h5>
                <p>{field}</p>
            </Fragment>
        ) : null;
    }

    static fieldCommunityImage( field ) {
        return field ? (
            <div className="o-community-image">
                <img alt={field.alt ? field.alt : 'Headshot'}
                     className="alignleft"
                     src={field.url} />
            </div>
        ) : null;
    }

    displayTestimonial() {
        const {
            acf: { testimonial_group }
        } = this.props;

        return (
            <div className="widget l-community-test-page-section u-cf">
                <hr className="o-divider" />
                <div className="l-community-top u-cf">
                    {CommunityTestimonial.fieldCommunityImage(testimonial_group.image_headshot)}
                    <div className="o-community-copy u-cf">
                        <h3 className="widget-title">{testimonial_group.name}</h3>
                        <p>{testimonial_group.status}</p>
                        {CommunityTestimonial.fieldHomeTown(testimonial_group.hometown)}
                        {CommunityTestimonial.fieldAlmaMater(testimonial_group.alma_mater)}
                    </div>
                </div>
                <div className="l-community-quote u-cf">
                    <blockquote dangerouslySetInnerHTML={reactHtml(testimonial_group.testimonial_quote)} />
                </div>
            </div>
        )
    }

    displayCommunity() {
        const {
            acf: { community_group }
        } = this.props;

        return (
            <Fragment>
                <hr className="o-divider" />
                <div className="widget l-in-community l-feature-block">
                    <h2 className="widget_title">{community_group.section_title}</h2>
                    <div className="o-section-image"><img
                        className="alignleft"
                        alt={community_group.section_image.alt !== '' ? community_group.section_image.alt : 'Feature'}
                        src={community_group.section_image.url} />
                    </div>
                    <div className="o-section-caption" dangerouslySetInnerHTML={reactHtml( community_group.section_image_caption )} />
                </div>
            </Fragment>

        )
    }

    render() {
        const {
            acf: {
                display_testimonial,
                display_community
            }
        } = this.props;

        return (
            <section className="widget l-community u-cf">
                {display_testimonial === 'yes' ? this.displayTestimonial() : null}
                {display_community === 'yes' ? this.displayCommunity() : null}
            </section>
        );
    }
}

CommunityTestimonial.propTypes = {
    acf: PropTypes.oneOfType([
        PropTypes.object,
        PropTypes.bool
    ])
};

export default CommunityTestimonial;
