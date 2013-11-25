# the-fr [[dot](http://the-fr.org)] org: API documentation

API functions are accessed by appending /api/ to the primary URL of
the site, e.g. the-fr.org, and further, appending the documented strings
below, depending on the function you wish to access.

## Supported API functions

The following is an exhaustive list of the functionality supported by 
the-fr.org:

----

### Alternative Identification Schemes

Alternative identifications schemes allow users to explore the-fr.org
using known file-format identifiers. 

Currently supported are [*PRONOM unique identifiers*](http://en.wikipedia.org/wiki/PRONOM#The_PRONOM_Persistent_Unique_Identifier_.28PUID.29_scheme). In future identifiers
such as UDFR will be supported. 

URI examples are as follows:

    /api/id/puid/fmt/8
    /api/id/udfr/u1f378

----

**PUIDS: PRONOM Unique Identifiers**

There are two types of PUID, fmt, and x-fmt. You can access records 
matching a published PUID using the following notation:

    /api/id/puid/fmt/{no}
    /api/id/puid/x-fmt/{no}

And:

    /api/id/puid/xfmt/{no}

the-fr.org examples:

- [PUID: fmt/8](http://the-fr.org/api/id/puid/fmt/8)
- [PUID: x-fmt/8](http://the-fr.org/api/id/puid/x-fmt/8)

**Return types:**

The function will currently re-direct the agent to the record matching
the requested PUID. 

*N.B.* It may be necessary to eventually provide agents using this function call
with a listing page where a PUID matches multiple the-fr.org records. 

----

&nbsp;

    Product of:
[exponentialdecay.co.uk](http://exponentialdecay.co.uk/blog)

[@beet_keeper](http://twitter.com/beet_keeper)
