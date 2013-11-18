# The Format Registry [[dot](http://the-fr.org)] org
**[the-fr.org](http://the-fr.org)**

Welcome to The Format Registry: A linked data file format registry.

The work is the result of a four-day hack during November 2013. Its 
goal is to influence the rapid development of further format registries and 
linked open data initiatives within the digital preservation community.

The focus of this project will be on the data and the augmenting of what is 
currently available.

There will be blogs and discussions on the architecture of this site on my primary
host @ [exponentialdecay.co.uk](http://exponentialdecay.co.uk/blog)

----

## Source code

**GitHub:** [GitHub](https://github.com/exponential-decay)

-----

## Data features...

**File formats:** http://the-fr.org/id/file-format/{no}

&nbsp; &nbsp; &nbsp; e.g. <http://the-fr.org/id/file-format/8>

**Definitions:** http://the-fr.org/def/format-registry/{Class}

&nbsp; &nbsp; &nbsp; e.g. <http://the-fr.org/def/format-registry/FileFormat>

**Properties:** http://the-fr.org/prop/format-registry/{property}

&nbsp; &nbsp; &nbsp; e.g. <http://the-fr.org/prop/format-registry/puid>

**Alt. formats:** http://the-fr.org/data/file-format/{no}.(rdf | json | ttl | tsv | html)

&nbsp; &nbsp; &nbsp; e.g. <http://the-fr.org/data/file-format/8.rdf> 

&nbsp; &nbsp; &nbsp; e.g. <http://the-fr.org/data/file-format/8.json>

**N.B.** *Predicate data is not currently handled via .htaccess please use the SPARQL endpoint.*

----

## SPARQL Endpoint

## [http://the-fr.org/public/sparql/endpoint.php](http://the-fr.org/public/sparql/endpoint.php)

----

### Sample queries

**List raster image formats:** [Query](http://the-fr.org/public/sparql/endpoint.php?query=select+distinct+%3Fs+%3Flabel+where+%7B+%0D%0A%3Fs+%3Chttp%3A%2F%2Fthe-fr.org%2Fprop%2Fformat-registry%2FformatType%3E+%3Chttp%3A%2F%2Fthe-fr.org%2Fdef%2Fformat-registry%2FRasterImage%3E+.+%0D%0A%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F2000%2F01%2Frdf-schema%23label%3E+%3Flabel+.+%0D%0A%7D+limit+200&output=tsv&jsonp=&key=&show_inline=1)

       select distinct ?s ?label where 
       { 
          ?s <http://the-fr.org/prop/format-registry/formatType> <http://the-fr.org/def/format-registry/RasterImage> . 
          ?s <http://www.w3.org/2000/01/rdf-schema#label> ?label . 
       } 
       limit 200

**Count the number of raster image formats:** [Query](http://the-fr.org/public/sparql/endpoint.php?query=select+%28count%28%3Fs%29+as+%3Frastercount%29+where+%7B+%0D%0A%3Fs+%3Chttp%3A%2F%2Fthe-fr.org%2Fprop%2Fformat-registry%2FformatType%3E+%3Chttp%3A%2F%2Fthe-fr.org%2Fdef%2Fformat-registry%2FRasterImage%3E+.+%0D%0A%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F2000%2F01%2Frdf-schema%23label%3E+%3Flabel+.+%0D%0A%7D+limit+250%0D%0A&output=tsv&jsonp=&key=&show_inline=1)

       select (count(?s) as ?rastercount) where 
       { 
          ?s <http://the-fr.org/prop/format-registry/formatType> <http://the-fr.org/def/format-registry/RasterImage> . 
          ?s <http://www.w3.org/2000/01/rdf-schema#label> ?label . 
       } 
       limit 250

**List of properties used across the database:** [Query](http://the-fr.org/public/sparql/endpoint.php?query=select+distinct+%3Fp+where+%7B+%0D%0A+++%3Fs+%3Fp+%3Fo+.%0D%0A%7D+limit+200&output=tsv&jsonp=&key=&show_inline=1)

       select distinct ?p where 
       { 
          ?s ?p ?o .
       } 
       limit 200

----
### N-Triples

The .NT files are a product of the mapping process, atm: DOWNLOAD FROM SOURCE => MAP TO .NT
=> LOAD INTO TRIPLESTORE. They might be useful in their own right to some. Currently unzipped. 

**Format triples:** [tfr-format-triples.nt](http://the-fr.org/public/tfr/tfr-triples/tfr-format-triples.nt)

**Format ontology triples:** [tfr-ontology-triples.nt](http://the-fr.org/public/tfr/tfr-triples/tfr-ontology-triples.nt)

----

### Legal

This site is bought to you by aggregating numerous other resources. If those sources
appear on this site, they are listed below. 

- [PRONOM](http://www.nationalarchives.gov.uk/PRONOM/Default.aspx) data is licensed 
under the Open Government Licence (OGL): <http://www.nationalarchives.gov.uk/doc/open-government-licence/>

----

&nbsp;

    Product of:
[exponentialdecay.co.uk](http://exponentialdecay.co.uk/blog)

[@beet_keeper](http://twitter.com/beet_keeper)
