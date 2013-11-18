- Check latest signature file version in release notes
- Use modified date as a mechanism for not overwriting an archive
- Reread release note date if it fails
- Checksum maybe
- User-agent string for all requests to PRONOM
- Move folder locations around code better. 
- Escape folders manually, i.e. not in folder variable, and consistently
- Access restriction on folders - pronom-data private / pronom-archive public
- Multi-thread PRONOM xml read
- Guarantee uri puid mapping
- Re-write url for x-fmt and fmt puids
- Container signatures: http://www.nationalarchives.gov.uk/pronom/container-signature.xml
- Stats out, e.g. triple load stats to db
- Stats redirect, e.g. /stats -> stats.php / stats.md
- Map LastUpdatedDate from PRONOM 
- Consider what to do with ReleaseDate / WithdrawnDate / FormatNote
- Provenance
- Internal Signatures and writing engine

Outstanding return message / error on some release-notes.xml read attempts
--
[Sun Nov 17 10:46:56.729134 2013] [:error] [pid 8615] [client 127.0.0.1:46645] PHP Warning:  file_get_contents(): stream does not support seeking in /var/www/index.php on line 95
[Sun Nov 17 10:46:56.729254 2013] [:error] [pid 8615] [client 127.0.0.1:46645] PHP Warning:  file_get_contents(): Failed to seek to position 234 in the stream in /var/www/index.php on line 95
--

Unused predicates
--

http://the-fr.org/format-registry/lastUpdatedPronom

modifier: "2001-07-01"^^http://www.w3.org/2001/XMLSchema#date

http://the-fr.org/format-registry/releaseDate	 

http://the-fr.org/format-registry/withdrawnDate	 

