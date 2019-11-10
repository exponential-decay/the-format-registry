<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	            xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	            xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	            xmlns:owl="https://www.w3.org/2002/07/owl#"
	            xmlns:dcterms="http://purl.org/dc/terms/"
	            xmlns:skos="http://www.w3.org/2004/02/skos/core#"
	            xmlns:tfr="http://the-fr.org/format-registry/"
	            xmlns:digipres="http://digipres.org/formats/sources/pronom/formats/#"
	            xmlns:tfrprop="http://the-fr.org/prop/format-registry/"
	            xmlns:wikidata="http://www.wikidata.org/prop/direct/"
			    version="1.0">
  <xsl:output method="text" encoding="UTF-8" indent="no" mediatype="text/x-markdown"/>
  <xsl:template match="rdf:RDF"><xsl:variable name="about"><xsl:value-of select="rdf:Description/@rdf:about"/></xsl:variable>
		##<xsl:value-of select="rdf:Description/rdfs:label"/>
		<xsl:text></xsl:text>
		###[<xsl:value-of select="$about"/>](<xsl:value-of select="$about"/>)
		<xsl:for-each select="rdf:Description">
		**Name:** <xsl:value-of select="rdfs:label"/>

		**Version:** <xsl:value-of select="tfrprop:version"/>

		**Description:** <xsl:value-of select="dcterms:description"/>

		**Deprecated:** <xsl:value-of select="tfrprop:isDeprecated"/>

		**MIMEType:** <xsl:value-of select="tfrprop:internetMediaType"/>

		**PUID:** <xsl:value-of select="tfrprop:puid"/>

		**sameAs : PRONOM:** [<xsl:value-of select="owl:sameAs/@rdf:resource"/>](<xsl:value-of select="owl:sameAs/@rdf:resource"/>)

		**Extension:**  <xsl:value-of select="tfrprop:hasExtension"/>

		**Magic:**  <xsl:value-of select="digipres:hasMagic"/>

		**Container Magic:**  <xsl:value-of select="tfrprop:hasPRONOMContainerMagic"/>

		**Binary Magic:**  <xsl:value-of select="tfrprop:hasPRONOMBinaryMagic"/>

		**Signature Priority Over:**
		<xsl:for-each select="tfrprop:hasPriorityOver">
   			* [<xsl:value-of select="@rdf:resource"/>](<xsl:value-of select="@rdf:resource"/>)
		</xsl:for-each>

		**See Also (e.g. Wikidata, Library of Congress):**
			<xsl:for-each select="rdfs:seeAlso">
				* [<xsl:value-of select="@rdf:resource"/>](<xsl:value-of select="@rdf:resource"/>)</xsl:for-each>

		**Software that can read the format:**
			<xsl:for-each select="wikidata:P1072">
				* [<xsl:value-of select="@rdf:resource"/>](<xsl:value-of select="@rdf:resource"/>)</xsl:for-each>

		**Alias:** <xsl:value-of select="skos:altLabel"/>

		**Class:** [<xsl:value-of select="rdf:type/@rdf:resource"/>](<xsl:value-of select="rdf:type/@rdf:resource"/>)

		**Type:** [<xsl:value-of select="tfrprop:formatType/@rdf:resource"/>](<xsl:value-of select="tfrprop:formatType/@rdf:resource"/>)

		**SPARQL:** [http://the-fr.org/public/sparql/endpoint.php?query=describe+%3C<xsl:value-of select="$about"/>%3E&amp;output=&amp;jsonp=&amp;key=&amp;show_inline=1](http://the-fr.org/public/sparql/endpoint.php?query=describe+%3C<xsl:value-of select="$about"/>%3E&amp;output=&amp;jsonp=&amp;key=&amp;show_inline=1)


		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
