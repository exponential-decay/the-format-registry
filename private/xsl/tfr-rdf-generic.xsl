<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
   xmlns:owl="https://www.w3.org/2002/07/owl#"
	xmlns:dcterms="http://purl.org/dc/terms/"
	xmlns:skos="http://www.w3.org/2004/02/skos/core#"
	xmlns:tfr="http://the-fr.org/format-registry/"
	xmlns:tfrprop="http://the-fr.org/prop/format-registry/">

<xsl:output method="text" encoding="UTF-8" indent="no" mediatype="text/x-markdown"/>
	<xsl:template match="rdf:RDF">
		##<xsl:value-of select="rdf:Description/rdfs:label"/>
		<xsl:text>&#10;&#10;</xsl:text>
		###[<xsl:value-of select="rdf:Description/@rdf:about"/>](<xsl:value-of select="rdf:Description/@rdf:about"/>)
		<xsl:for-each select="rdf:Description">
		&#10;
		**Name:** <xsl:value-of select="rdfs:label"/>&#10;
		**Version:** <xsl:value-of select="tfrprop:version"/>&#10; 		
		**Description:** <xsl:value-of select="dcterms:description"/>&#10;
		**MIMEType:** <xsl:value-of select="tfrprop:internetMediaType"/>&#10;
		**PUID:** <xsl:value-of select="tfrprop:puid"/>&#10;
		**sameAs : PRONOM:** [<xsl:value-of select="owl:sameAs/@rdf:resource"/>](<xsl:value-of select="owl:sameAs/@rdf:resource"/>)&#10;
		**Extension:**  <xsl:value-of select="tfrprop:hasExtension"/>&#10;
		**Alias:** <xsl:value-of select="skos:altLabel"/>&#10;
		**Class:** [<xsl:value-of select="rdf:type/@rdf:resource"/>](<xsl:value-of select="rdf:type/@rdf:resource"/>)&#10;
		**Type:** [<xsl:value-of select="tfrprop:formatType/@rdf:resource"/>](<xsl:value-of select="tfrprop:formatType/@rdf:resource"/>)&#10;
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>

