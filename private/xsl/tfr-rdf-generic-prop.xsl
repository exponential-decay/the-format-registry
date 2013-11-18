<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	xmlns:dcterms="http://purl.org/dc/terms/"
	xmlns:skos="http://www.w3.org/2004/02/skos/core#"
	xmlns:tfr="http://the-fr.org/format-registry/"
	xmlns:tfrprop="http://the-fr.org/prop/format-registry/"
	xmlns:status="http://www.w3.org/2003/06/sw-vocab-status/ns#">

<xsl:output method="text" encoding="UTF-8" indent="no" mediatype="text/x-markdown"/>
	<xsl:template match="rdf:RDF">
		##<xsl:value-of select="rdf:Description/rdfs:label"/>
		<xsl:text>&#10;&#10;</xsl:text>
		###[<xsl:value-of select="rdf:Description/@rdf:about"/>](<xsl:value-of select="rdf:Description/@rdf:about"/>)
		<xsl:for-each select="rdf:Description">
		&#10;
		**Name:** <xsl:value-of select="rdfs:label"/>&#10;
		**SubclassOf:** [<xsl:value-of select="rdfs:subClassOf/@rdf:resource"/>](<xsl:value-of select="rdfs:subClassOf/@rdf:resource"/>)&#10; 		
		**Description:** <xsl:value-of select="rdfs:comment"/>&#10;
		**isDefinedBy:**  [<xsl:value-of select="rdfs:isDefinedBy/@rdf:resource"/>](<xsl:value-of select="rdfs:isDefinedBy/@rdf:resource"/>)&#10;
		**SeeAlso:** [<xsl:value-of select="rdfs:seeAlso/@rdf:resource"/>](<xsl:value-of select="rdfs:seeAlso/@rdf:resource"/>)&#10;
		**Class:** [<xsl:value-of select="rdf:type/@rdf:resource"/>](<xsl:value-of select="rdf:type/@rdf:resource"/>)&#10;
		**Type:** [<xsl:value-of select="tfrprop:formatType/@rdf:resource"/>](<xsl:value-of select="tfr:formatType/@rdf:resource"/>)&#10;
		**Status** <xsl:value-of select="status:term_status"/>&#10;
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>





