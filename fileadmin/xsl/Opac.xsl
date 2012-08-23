<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="xml" indent="yes"/>

    <!-- Copy
    <xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()"/>
        </xsl:copy>
    </xsl:template>
    -->

    <xsl:template match="html">
        <div class="opac">
            <div class="menu">
                <h2 class="hidden">Menu</h2>
                <ul class="menu1">

                    <xsl:for-each
                        select="//td[@class='nav']/descendant::td[@class='nav0']
                                | //td[@class='nav']/descendant::td[@class='nav1']">
                        <li>
                            <a>
                                <xsl:attribute name="href">
                                    <xsl:value-of select="descendant-or-self::a/@href"/>
                                </xsl:attribute>
                                <xsl:value-of select="normalize-space(./descendant::a)"/>
                                <xsl:if test="not(./descendant::a)">
                                    <xsl:value-of select="normalize-space(./text())"/>
                                </xsl:if>
                            </a>
                        </li>
                    </xsl:for-each>
                </ul>

                <ul class="menu2">
                    <xsl:for-each select="//a[@class='mnu']">
                        <li>
                            <a>
                                <xsl:attribute name="href">
                                    <xsl:value-of select="descendant-or-self::a/@href"/>
                                </xsl:attribute>
                                <xsl:value-of select="normalize-space(./descendant::a)"/>
                                <xsl:if test="not(./descendant::a)">
                                    <xsl:value-of select="normalize-space(./text())"/>
                                </xsl:if>
                            </a>
                        </li>
                    </xsl:for-each>
                </ul>
            </div>

            <div class="searchForm">
                <form>
                    <xsl:copy-of select="//form[@name='SearchForm']/@*"/>
                    <xsl:copy-of select="//td[@class='cmd']/descendant::select
                                | //td[@class='cmd']/descendant::input"
                    />
                </form>
            </div>

            <div class="content">
                <xsl:copy-of select="//td[@class='cnt']/*"/>
            </div>


        </div>
    </xsl:template>


</xsl:stylesheet>
