<?php
	define("MEDIATYPE_PREDICATE", "<http://the-fr.org/prop/format-registry/internetMediaType>");
	define("PUID_PREDICATE", "<http://the-fr.org/prop/format-registry/puid>");
	define("CLASS_PREDICATE", "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>");
	define("NAME_PREDICATE", "<http://www.w3.org/2000/01/rdf-schema#label>");
	define("VERSION_PREDICATE", "<http://the-fr.org/prop/format-registry/version>");
	define("DESCRIPTION_PREDICATE", "<http://purl.org/dc/terms/description>");
	define("TYPE_PREDICATE", "<http://the-fr.org/prop/format-registry/formatType>");
	define("ALIAS_PREDICATE", "<http://www.w3.org/2004/02/skos/core#altLabel>");
	define("EXTENSION_PREDICATE", "<http://the-fr.org/prop/format-registry/hasExtension>");
   define("CONTAINER_PREDICATE", "<http://the-fr.org/prop/format-registry/hasPRONOMContainerMagic>");
   define("BINARY_PREDICATE", "<http://the-fr.org/prop/format-registry/hasPRONOMBinaryMagic>");
   define("DEPRECATED_PREDICATE", "<http://the-fr.org/prop/format-registry/isDeprecated>");

   //file format relationships
   define("HASPRIORITY_PREDICATE", "<http://the-fr.org/prop/format-registry/hasPriorityOver>");

   // additional ontologies
   define("SAMEAS_PREDICATE", "<https://www.w3.org/2002/07/owl#sameAs>");
   define("MAGIC_PREDICATE", "<http://digipres.org/formats/sources/pronom/formats/#hasMagic>");   
?>
