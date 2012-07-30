<!--

    The contents of this file are subject to the license and copyright
    detailed in the LICENSE and NOTICE files at the root of the source
    tree and available online at

    http://www.dspace.org/license/

-->
<!--
    Main structure of the page, determines where
    header, footer, body, navigation are structurally rendered.
    Rendering of the header, footer, trail and alerts

    Author: art.lowel at atmire.com
    Author: lieven.droogmans at atmire.com
    Author: ben at atmire.com
    Author: Alexey Maslov

-->

<xsl:stylesheet xmlns:i18n="http://apache.org/cocoon/i18n/2.1"
	xmlns:dri="http://di.tamu.edu/DRI/1.0/"
	xmlns:mets="http://www.loc.gov/METS/"
	xmlns:xlink="http://www.w3.org/TR/xlink/"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns:mods="http://www.loc.gov/mods/v3"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="i18n dri mets xlink xsl dim xhtml mods dc">

    <xsl:output indent="yes"/>
    <xsl:template match="dri:i18n"/>
    <!-- global variables -->
    <xsl:variable name="adwPortalBaseURL"><xsl:text>http://adw-goe.de</xsl:text></xsl:variable>
    <xsl:variable name="langcode">
	<xsl:if test="/dri:document/dri:meta/dri:pageMeta/dri:metadata[@element='locale'] = 'de'"></xsl:if>
        <xsl:if test="/dri:document/dri:meta/dri:pageMeta/dri:metadata[@element='locale'] = 'en'">/en</xsl:if>
    </xsl:variable>
    <!--
        The starting point of any XSL processing is matching the root element. In DRI the root element is document,
        which contains a version attribute and three top level elements: body, options, meta (in that order).

        This template creates the html document, giving it a head and body. A title and the CSS style reference
        are placed in the html head, while the body is further split into several divs. The top-level div
        directly under html body is called "ds-main". It is further subdivided into:
            "ds-header"  - the header div containing title, subtitle, trail and other front matter
            "ds-body"    - the div containing all the content of the page; built from the contents of dri:body
            "ds-options" - the div with all the navigation and actions; built from the contents of dri:options
            "ds-footer"  - optional footer div, containing misc information

        The order in which the top level divisions appear may have some impact on the design of CSS and the
        final appearance of the DSpace page. While the layout of the DRI schema does favor the above div
        arrangement, nothing is preventing the designer from changing them around or adding new ones by
        overriding the dri:document template.
    -->
    <xsl:template match="dri:document">
       
                  
                        <div id="ds-content-wrapper">
                            <div id="ds-content" class="clearfix">
			<!--The trail is built by applying a template over pageMeta's trail children. -->
                        <xsl:call-template name="buildTrail"/>
                                <!--
                               Goes over the document tag's children elements: body, options, meta. The body template
                               generates the ds-body div that contains all the content. The options template generates
                               the ds-options div that contains the navigation and action options available to the
                               user. The meta element is ignored since its contents are not processed directly, but
                               instead referenced from the different points in the document. -->
			
                                <xsl:apply-templates/>
                            </div>
                        </div>
                
    </xsl:template>

 
    <xsl:template name="buildTrail">
        <div id="ds-trail-wrapper">
            <ul id="ds-trail">
                <xsl:choose>
                    <xsl:when test="count(/dri:document/dri:meta/dri:pageMeta/dri:trail) = 0">
                        <li class="ds-trail-link first-link">-</li>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="/dri:document/dri:meta/dri:pageMeta/dri:trail"/>
                    </xsl:otherwise>
                </xsl:choose>
            </ul>
        </div>
    </xsl:template>

    <xsl:template match="dri:trail">
        <!--put an arrow between the parts of the trail-->
        <xsl:if test="position()>1">
            <li class="ds-trail-arrow">
                <xsl:text>&#8594;</xsl:text>
            </li>
        </xsl:if>
        <li>
            <xsl:attribute name="class">
                <xsl:text>ds-trail-link </xsl:text>
                <xsl:if test="position()=1">
                    <xsl:text>first-link </xsl:text>
                </xsl:if>
                <xsl:if test="position()=last()">
                    <xsl:text>last-link</xsl:text>
                </xsl:if>
            </xsl:attribute>
            <!-- Determine whether we are dealing with a link or plain text trail link -->
            <xsl:choose>
                <xsl:when test="./@target">
                    <a>
                        <xsl:attribute name="href">
                            <xsl:value-of select="./@target"/>
                        </xsl:attribute>
                        <xsl:apply-templates />
                    </a>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates />
                </xsl:otherwise>
            </xsl:choose>
        </li>
    </xsl:template>





<!--
        The meta, body, options elements; the three top-level elements in the schema
-->




    <!--
        The template to handle the dri:body element. It simply creates the ds-body div and applies
        templates of the body's child elements (which consists entirely of dri:div tags).
    -->
    <xsl:template match="dri:body">
        <div id="ds-body">
            <xsl:if test="/dri:document/dri:meta/dri:pageMeta/dri:metadata[@element='alert'][@qualifier='message']">
                <div id="ds-system-wide-alert">
                    <p>
                        <xsl:copy-of select="/dri:document/dri:meta/dri:pageMeta/dri:metadata[@element='alert'][@qualifier='message']/node()"/>
                    </p>
                </div>
            </xsl:if>
            <xsl:apply-templates />
        </div>
    </xsl:template>


    <!-- Currently the dri:meta element is not parsed directly. Instead, parts of it are referenced from inside
        other elements (like reference). The blank template below ends the execution of the meta branch -->
    <xsl:template match="dri:meta">
    </xsl:template>



</xsl:stylesheet>
