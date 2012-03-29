<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="xml"/>



	<!-- Copy -->
	<xsl:template match="@*|node()">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/>
		</xsl:copy>
	</xsl:template>



	<!--
		Add Base URL to relative links for images, scripts and CSS.
	-->
	<xsl:template match="xhtml:img/@src | xhtml:link/@href | xhtml:script/@src | xhtml:a/@href
							| img/@src | link/@href | script/@src">
		<xsl:attribute name="{local-name(.)}">
			<xsl:choose>
				<xsl:when test="contains(., 'http://rep.adw-goe.de/DRE')">
					<xsl:text>http://rep.adw-goe.de/</xsl:text>
					<xsl:value-of select="substring-after(., 'http://rep.adw-goe.de/DRE')"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="."/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</xsl:template>


</xsl:stylesheet>
