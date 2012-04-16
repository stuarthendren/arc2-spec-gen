<?php
/*
homepage: http://stuarthendren.net/arcspecgen
license:  http://opensource.org/licenses/mit-license.php

class:    ARC2 Specification Generator
author:   Stuart Hendren
version:  2008-09-29
*/

/*
ARS2_SpecGen v1, ontology specification generator tool

Copyright (c) 2008 Stuart Hendren <me@stuarthendren.net>

This is based on the SpecGen software by Christopher Schmidt, Uldis Bojars and Sergio Fernández
I have adapted it for use in ARC http://arc.semsol.org/.

Original version  is available at https://bitbucket.org/wikier/specgen/wiki/Home
*/

ARC2::inc('Store');

class ARC2_SpecGenPlugin extends ARC2_Store {

	function __construct($a = '', &$caller) {
	  parent::__construct($a, $caller);
	}

	function ARC2_SpecGenPlugin ($a = '', &$caller) {
	  $this->__construct($a, $caller);
	}

	function __init() {
	  parent::__init();
	}

	// -----------
	// Plugin code
	// -----------

	function setGlobals($prefix, $omits){

		// Sets a number of global variables
		global $g;
		$g = array(
		'ns_list' => array(
			'xsd:' => 'http://www.w3.org/2001/XMLSchema#',
  			'rdf:' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
			'rdfa:' => 'http://www.w3.org/1999/xhtml/vocab#',
  			'rdfs:' => 'http://www.w3.org/2000/01/rdf-schema#',
			'owl:' => 'http://www.w3.org/2002/07/owl#',
  			'foaf:' => 'http://xmlns.com/foaf/0.1/',
  			'dc:' => 'http://purl.org/dc/elements/1.1/',
  			'dct:' => 'http://purl.org/dc/terms/',
  			'skos:' => 'http://www.w3.org/2004/02/skos/core#',
  			'sioc:' => 'http://rdfs.org/sioc/ns#',
  			'sioct:' => 'http://rdfs.org/sioc/types#',
  			'xfn:' => 'http://gmpg.org/xfn/11#',
  			'twitter:' => 'http://twitter.com/',
  			'rss:' => 'http://purl.org/rss/1.0/',
  			'doap:' => 'http://usefulinc.com/ns/doap#',
        	'status:' => 'http://www.w3.org/2003/06/sw-vocab-status/ns#',
        	'content:' => 'http://purl.org/rss/1.0/modules/content/',
        	'geo:' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
        	'vs:' => 'http://www.w3.org/2003/06/sw-vocab-status/ns#',
        	'cc:' => 'http://creativecommons.org/licenses/',
        	'bio:' => 'http://vocab.org/bio/0.1/',
        	'contact:' => 'http://www.w3.org/2000/10/swap/pim/contact#',
        	'ical:' => 'http://www.w3.org/2002/12/cal/icaltzd#',
        	'rel:' => 'http://vocab.org/relationship/',
        	'openid:' => 'http://xmlns.openid.net/auth#',
        	'wot:' => 'http://xmlns.com/wot/0.1/'
		),
		'prefix' => 'PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> .
    		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
        	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
        	PREFIX owl:  <http://www.w3.org/2002/07/owl#> .
        	PREFIX site: <http://www.stuarthendren.net/site.rdf#> .
        	PREFIX sioc: <http://rdfs.org/sioc/ns#> .
        	PREFIX sioct: <http://rdfs.org/sioc/types#> .
        	PREFIX cc: <http://creativecommons.org/licenses/> .
        	PREFIX bio: <http://vocab.org/bio/0.1/> .
        	PREFIX contact: <http://www.w3.org/2000/10/swap/pim/contact#> .
        	PREFIX dc: <http://purl.org/dc/elements/1.1/> .
        	PREFIX dct: <http://purl.org/dc/terms/> .
        	PREFIX foaf: <http://xmlns.com/foaf/0.1/> .
        	PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> .
        	PREFIX ical: <http://www.w3.org/2002/12/cal/icaltzd#> .
        	PREFIX rel: <http://vocab.org/relationship/> .
        	PREFIX openid: <http://xmlns.openid.net/auth#> .
        	PREFIX wot: <http://xmlns.com/wot/0.1/> .
        	PREFIX xfn: <http://gmpg.org/xfn/11#> .
  			PREFIX twitter: <http://twitter.com/> .
  			PREFIX rss: <http://purl.org/rss/1.0/> .
  			PREFIX doap: <http://usefulinc.com/ns/doap#> .
        	PREFIX status: <http://www.w3.org/2003/06/sw-vocab-status/ns#> .
       	 	PREFIX content: <http://purl.org/rss/1.0/modules/content/> .
        	PREFIX skos: <http://www.w3.org/2004/02/skos/core#> .
        	PREFIX vs: <http://www.w3.org/2003/06/sw-vocab-status/ns#> .',
        'omit' => array('rdfs:comment',
				  'rdfs:label',
                  'rdf:type',
                  'rdfs:subClassOf',
                  'super class of',
                  'in range of',
                  'in domain of',
				  'rdfs:subPropertyOf',
				  'rdfs:range',
                  'rdfs:domain',
                  'super property of',

                  ),
        'include' => array('rdfs:comment',
                     'rdfs:label',
                     'rdfs:domain',
                     'rdfs:range',
                     'rdfs:subPropertyOf',
                     'owl:equivalentProperty',
                     'mms:definition',
                     'mms:repeatable',
                     'mms:obligation',
                     'mms:mmsName',
                     )
        	);

    	//Get spec uri from store variables

    	$q = $g['prefix'];
		$q .= 'SELECT ?s ';
		$q .= 'FROM <http://example.com/specgen>';
		$q .= 'WHERE { ?s rdf:type owl:Ontology .}';
	    $rs = $this->query($q);
    	if (!$this->getErrors()) {
  			$spec_uri = $rs['result']['rows'][0]['s'];
  			//echo "Spec uri " . $spec_uri;
  		} else {
  			echo $this->getErrors();
  			exit("Unable to obtain uri of specification");
  		}

		$g['spec_uri'] = $spec_uri;
		$g['spec_pre'] = $prefix;
		$g['ns_list'][$g['spec_pre']] = $g['spec_uri'];
		foreach($omits as $omit){
			$g['omit'][] = $omit;
		}

	}


	// Writes a prefixed name instad of the full uri
	function getPrefixedName($uri){
		global $g;
		if( strstr($uri, $g['spec_uri']) ){
			$uri = str_replace($g['spec_uri'] . "#", $g['spec_pre'], $uri);
			$uri = str_replace($g['spec_uri'], $g['spec_pre'], $uri);
		} else {
			foreach ($g['ns_list'] as $prefix=>$address) {
				$uri = str_replace($address,$prefix, $uri);
  			}
		}
  		return $uri;
	}

	// Return owl:versionInfo
	function owlVersionInfo($g){
		global $g;
		$q = $g['prefix'];
		$q .= 'SELECT ?info ';
		$q .= 'FROM <http://example.com/specgen>';
             '?info WHERE { <' . $g['spec_uri'] . '> owl:versionInfo ?info .}';

	    $rs = $this->query($q);
    	$return = (!empty($rs['result']['rows'][0])) ? $rs['result']['rows'][0] : ' ';
    	return ($return);
  	}

	function parseCollection(){
		//def parseCollection(model, collection):
		//    # #propertyA a rdf:Property ;
		//    #   rdfs:domain [
		//    #      a owl:Class ;
		//    #      owl:unionOf [
		//    #        rdf:parseType Collection ;
		//    #        #Foo a owl:Class ;
		//    #        #Bar a owl:Class
		//    #     ]
		//    #   ]

	}

	function getTermLink($uri){
		global $g;
		if(strstr($uri, $g['spec_uri'])){
			$anchor = $this->getShortName($uri);
			$getPrefixedName = $this->getPrefixedName($uri);
			$link = '<a href="#term_' . $anchor . '">'. $getPrefixedName . '</a>';
		} else {
			$getPrefixedName = $this->getPrefixedName($uri);
			$link = '<a href="' . $uri . '">'. $getPrefixedName . '</a>';
		}
		return $link;
	}

	function getShortName($uri){
		if (strstr($uri, "#")){
        	return substr($uri, stripos($uri, "#")+1);
		}
    	else {
        	return substr($uri, strripos($uri, "/")+1);
    	}
	}


	function getAnchor($uri){
		global $spec_uri;
		if($spec_uri < $uri){
			echo "get Anchor called by $uri";
			return str_replace( "/", "_", $spec_uri);
		}
		else{
			return getShortName($uri);
		}
	}

	function parseBlankNode($classInfo, $node){
		global $g;
		//TODO
		return "blank";
	}

	//Get array of classes in the specification
	function getClasses(){
		global $g;
		// This includes both owl and rdf Classes but not blank node that are usually part of owl:Restrictions
		$q = $g['prefix'];
		$q .= 'SELECT ?c ';
		$q .= 'FROM <http://example.com/specgen>';
		$q .= 'WHERE { ?c rdf:type ?type .';
		$q .= 'FILTER ( ?type = owl:Class || ?type = rdf:Class ) .';
		$q .= 'FILTER regex(str(?p), "' . $g['spec_uri'] .'") .'; // Filters definitions from other namspaces, delete line if wanted in specification
		$q .= 'FILTER (!isBlank(?c))';
		$q .= '} ORDER BY ?c';
	    $rs = $this->query($q);
	    $classes = array();
    	if (!$this->getErrors()) {
			foreach( $rs['result']['rows'] as $row){
				$classes[] = $row['c'];
			}
  		} else {
  			echo("<p>Warning: Unable to obtain class information</p>");
  		}
  		return $classes;
	}

	//This gets the information about the Class including range, domain and super class information by running a number of SPARQL queries
	function getInformation($subject){
		global $g;
		// This gets the direct information
		$q = $g['prefix'];
		$q .= 'DESCRIBE <' . $subject . '> ';
		$q .= 'FROM <http://example.com/specgen>';
	    $qs = $this->query($q);
	    if (!$this->getErrors()) {
			$index = $qs['result'];
			$res = $index[$subject];
			foreach($res as $predicate=>$objectArray){
				$objectValueArray = array();
				foreach($objectArray as $object){
					if($object['type'] == 'uri'){
						$objectValue = $this->getTermLink($object['value']);
					} elseif($object['type'] == 'iri'){
						$objectValue = $this->getTermLink($object['val']);  //For older versions of ARC
					} elseif ($object['type'] == 'literal'){
						$objectValue = $object['value'];
						//$objectValue = $object['val'];  //For older versions of ARC
					} elseif ($object['type'] == 'bnode'){
						$objectValue = $this->parseBlankNode($res, $object['value']);
						//$objectValue = $this->parseBlankNode($res, $object['val']);  //For older versions of ARC
					}
					$objectValueArray[] = $objectValue;
				}
				$info[$predicate]=$objectValueArray;
			}
  		} else {
  			echo("<p>Warning: Unable to obtain information for $subject</p>");
  		}
  		return $info;
	}
	//This function gets the inforamation that is particular to classes
	function getClassInformation($class){
		global $g;
		//Get the main info from describe query
		$classInfo = $this->getInformation($class);
  		// This gets the properties that $class is in the range of
  		// (note does not parse the subproperty tree, only returns directly stated properties
  		$r = $g['prefix'];
		$r .= 'SELECT ?p ';
		$r .= 'FROM <http://example.com/specgen>';
		$r .= 'WHERE { ?p rdfs:range <'. $class .'> }';
	    $rs = $this->query($r);
	    if (!empty($rs['results']['rows'])) {
	    	foreach( $rs['result']['rows'] as $row){
	    		$range[] = $this->getTermLink($row['p']);
			}
			$classInfo['in range of'] = $range;
  		}

  		// This gets the properties that $class is in the domain of
  		// (note does not parse the subproperty tree, only returns directly stated properties
  		$d = $g['prefix'];
		$d .= 'SELECT ?p ';
		$d .= 'FROM <http://example.com/specgen>';
		$d .= 'WHERE { ?p rdfs:domain <'. $class .'> }';
	    $ds = $this->query($d);
	    if (!empty($ds['result']['rows'])) {
	    	foreach( $ds['result']['rows'] as $row){
	    		$domain[] = $this->getTermLink($row['p']);
	    	}
			$classInfo['in domain of'] = $domain;
  		}

  		// This gets the Classes that $class is a direct super class of
  		$s = $g['prefix'];
		$s .= 'SELECT ?c ';
		$s .= 'FROM <http://example.com/specgen>';
		$s .= 'WHERE { ?c rdfs:subClassOf <'. $class .'> }';
	    $ss = $this->query($s);
	    if (!empty($ss['result']['rows'])) {
	    	foreach( $ss['result']['rows'] as $row){
				$super[] = $this->getTermLink($row['c']);
			}
			$classInfo['super class of'] = $super;
  		}

  		// This gets the Classes that $class is a direct super class of
  		$s = $g['prefix'];
		$s .= 'SELECT ?i ';
		$s .= 'FROM <http://example.com/specgen>';
		$s .= 'WHERE { ?i rdf:type <'. $class .'> }';
	    $ss = $this->query($s);
	    if (!empty($ss['result']['rows'])) {
	    	foreach( $ss['result']['rows'] as $row){
				$instance[] = $this->getTermLink($row['i']);
			}
			$classInfo['has instance'] = $instance;
  		}
		//serialize($classInfo);
  		return $classInfo;
	}

	function writeTripleBox($info, $property, $name = FALSE){
			global $g;
			if($name){
				$key = $name;
			} else {
				$key = $property;
			}
			if(!empty($info[$key])){
			$r .= '<td><strong><a href="'. $property . '">';
			if($name){
				$r .= $name;
			} else {
				$r .= $this->getPrefixedName($property);
			}
			$r .= "</a></strong></td>\n<td>";
			foreach($info[$key] as $object){
				$s .=", " . $object;
			}
			$r .=  substr($s, 2, strlen($s)-2);
			$r .= "</td>\n</tr>\n";
		}
		return $r;
	}

	function writeClassHTML($class){
		$classInfo = $this->getClassInformation($class);
		//serialize($classInfo);
		global $g;
		$r = '<div class="specterm" id="term_' . $this->getShortName($class) . '">' . "\n";
		$r .= "<h3>Class: " . $this->getShortName($class) . "</h3>\n";
		//$r .= "<h3>Class: " . $this->getPrefixedName($class) . "</h3>\n";  // For prefixed name instead of short name
		if(!empty($classInfo['http://purl.org/dc/elements/1.1/description'])){
			foreach($classInfo['http://purl.org/dc/elements/1.1/description'] as $description){
				$r .= "<p>" . $description . "</p>\n";
			}
		}
		if(!empty($classInfo['http://www.w3.org/2000/01/rdf-schema#comment'])){
			foreach($classInfo['http://www.w3.org/2000/01/rdf-schema#comment'] as $comment){
				$r .= "<p>" . $comment . "</p>\n";
			}
		}
		$r .= "<table>\n";

		//Write labels
		$r .= $this->writeTripleBox($classInfo, 'http://www.w3.org/2000/01/rdf-schema#label');

		//Write subclass info
		$r .= $this->writeTripleBox($classInfo, 'http://www.w3.org/2000/01/rdf-schema#subClassOf');

		//Write superclass info
		$r .= $this->writeTripleBox($classInfo, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'super class of');

		//Write domain of info
		$r .= $this->writeTripleBox($classInfo, 'http://www.w3.org/2000/01/rdf-schema#domain', 'in domain of');

		//Write range of info
		$r .= $this->writeTripleBox($classInfo, 'http://www.w3.org/2000/01/rdf-schema#range', 'in range of');

		//Write extra details
		foreach($classInfo as $predicate => $objectArray){
			$prefixedPredicate =$this->getPrefixedName($predicate);
			if(!in_array($prefixedPredicate, $g['omit'])){
				$r .= $this->writeTripleBox($classInfo, $predicate);
			}
		}
		$r .= "</table>\n";
		$r .= '<p style="float: right; font-size: small;">[<a href="#sec-glance">back to top</a>]</p>';
		$r .= "<br />\n</div>\n";
		return $r;
	}

	//function to remove duplicate entries
	function uniqueMultiArray($array, $sub_key) {
    	$target = array();
    	$existing_sub_key_values = array();
    	foreach ($array as $key=>$sub_array) {
        	if (!in_array($sub_array[$sub_key], $existing_sub_key_values)) {
            	$existing_sub_key_values[] = $sub_array[$sub_key];
            	$target[$key] = $sub_array;
        	}
    	}
    	return $target;
	}

	//Get array of classes in the specification
	function getProperties(){
		global $g;
		// This includes both owl and rdf Properties
		$q = $g['prefix'];
		$q .= ' SELECT ?p ?type ';
		$q .= 'FROM <http://example.com/specgen> ';
		$q .= 'WHERE { ?p rdf:type ?type . ';
		$q .= 'FILTER ( ?type = owl:ObjectProperty || ?type = owl:DatatypeProperty || ?type = owl:AnnotationProperty  || ?type = rdf:Property ) . ';
		$q .= 'FILTER regex(str(?p), "' . $g['spec_uri'] . '") . '; // Filters definitions from other namspaces, delete line if wanted in specification
		$q .= 'FILTER (!isBlank(?p))} ORDER BY ?p';

		$rs = $this->query($q);
    	if (!$this->getErrors()) {
			$properties = $rs['result']['rows'];
			//echo $properties;
		} else {
  			echo("<p>Warning: Unable to obtain property information</p>");
  		}
  		return $properties;
	}

	//This gets the information about the Class including range, domain and super class information by running a number of SPARQL queries
	function getPropertyInformation($property){
		global $g;
		//Get the main info from describe query
		$propertyInfo = $this->getInformation($property);
		//serialize($propertyInfo);
		// This gets the Properties that $property is a direct super property of
  		$q = $g['prefix'];
		$q .= 'SELECT ?c ';
		$q .= 'FROM <http://example.com/specgen>';
		$q .= 'WHERE { ?c rdfs:subPropertyOf <'. $property .'> . }';
	    $rs = $this->query($q);
	    if (!empty($rs['result']['rows'])) {
	    	foreach( $rs['result']['rows'] as $row){
				$super[] = $this->getTermLink($row['c']);
			}
			$propertyInfo['super property of'] = $super;
  		}
		//serialize($propertyInfo);
  		return $propertyInfo;
	}

	function writePropertyHTML($property){
		global $g;
		$propertyInfo = $this->getPropertyInformation($property['p']);
		//serialize($propertyInfo);

		$r = '<div class="specterm" id="term_' . $this->getShortName($property['p']) . '">' . "\n";
		$type = $this->getPrefixedName($property['type']);
		if($type == 'owl:ObjectProperty'){
			$r .= "<h3>Object Property: ";
		} elseif($type == 'owl:DatatypeProperty'){
			$r .= "<h3>Datatype Property: ";
		} elseif($type == 'owl:AnnotationProperty'){
			$r .= "<h3>Annotation Property: ";
		} else{
			$r .= "<h3>Property: ";
		}
		$r .= $this->getShortName($property['p']) . "</h3>\n";
		//$r .= $this->getShortName($property['p']) . "</h3>\n";  // For prefixed name instead of short name
		if(!empty($propertyInfo['http://purl.org/dc/elements/1.1/description'])){
			foreach($propertyInfo['http://purl.org/dc/elements/1.1/description'] as $description){
				$r .= "<p>" . $description . "</p>\n";
			}
		}

		if(!empty($propertyInfo['http://www.w3.org/2000/01/rdf-schema#comment'])){
			foreach($propertyInfo['http://www.w3.org/2000/01/rdf-schema#comment'] as $comment){
				$r .= "<p>" . $comment . "</p>\n";
			}
		}

		$r .= "<table>\n";

		//Write labels
		$r .= $this->writeTripleBox($propertyInfo, 'http://www.w3.org/2000/01/rdf-schema#label');

		//Write sub property info
		$r .= $this->writeTripleBox($propertyInfo, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf');

		//Write super property info
		$r .= $this->writeTripleBox($propertyInfo, 'http://www.w3.org/2000/01/rdf-schema#subProperty', 'super property of');

		//Write domain of info
		$r .= $this->writeTripleBox($propertyInfo, 'http://www.w3.org/2000/01/rdf-schema#domain');

		//Write range of info
		$r .= $this->writeTripleBox($propertyInfo, 'http://www.w3.org/2000/01/rdf-schema#range');

		//Write type of info
		$r .= $this->writeTripleBox($propertyInfo, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');

		//Write extra details
		foreach($propertyInfo as $predicate => $objectArray){
			$prefixedPredicate =$this->getPrefixedName($predicate);
			if(!in_array($prefixedPredicate, $g['omit'])){
				$r .= $this->writeTripleBox($propertyInfo, $predicate);
			}
		}
		$r .= "</table>\n";
		$r .= '<p style="float: right; font-size: small;">[<a href="#sec-glance">back to top</a>]</p>';
		$r .= "<br />\n</div>\n";
		return $r;
	}




	// Extract all resources instanced in the ontology (aka "everything that is not a class or a property")
	function getInstances(){
		global $g;
		// This excludes owl and rdf Classes, properties and blank nodes
		$q = $g['prefix'];
		$q .= 'SELECT ?c ';
		$q .= 'FROM <http://example.com/specgen>';
		$q .= 'WHERE { ?c rdf:type ?type .';
		$q .= 'FILTER regex(str(?p), "' . $g['spec_uri'] .'") .'; // Filters definitions from other namspaces, delete line if wanted in specification
		$q .= 'FILTER ( ?type != owl:Class ) .';
		$q .= 'FILTER ( ?type != rdf:Class ) .';
		$q .= 'FILTER ( ?type != owl:ObjectProperty ) .';
        $q .= 'FILTER ( ?type != owl:DatatypeProperty ) .';
        $q .= 'FILTER ( ?type != owl:AnnotationProperty ) .';
        $q .= 'FILTER ( ?type != owl:FunctionalProperty ) .';
        $q .= 'FILTER ( ?type != owl:InverseFunctionalProperty ) .';
        $q .= 'FILTER ( ?type != owl:SymmetricProperty ) .';
        $q .= 'FILTER ( ?type != rdf:Property ) .';
		$q .= 'FILTER (!isBlank(?c))} ORDER BY ?c';
	    $rs = $this->query($q);
	    $instances = array();
    	if (!$this->getErrors()) {
			foreach( $rs['result']['rows'] as $row){
				$instances[] = $row['c'];
			}
  		} else {
  			echo("<p>Warning: Unable to obtain instance information</p>");
  		}
  		return $instances;
	}

	//This function gets the inforamation that is particular to each instance
	function getInstanceInformation($instance){
		global $g;
		//Get the main info from describe query
		$instanceInfo = $this->getInformation($instance);
  		// This gets the properties that $class is in the range of (note does not parse the subproperty tree, only returns directly stated properties
  		$r = $g['prefix'];
		$r .= 'SELECT ?s ?p ';
		$r .= 'FROM <http://example.com/specgen>';
		$r .= 'WHERE { ?s ?p <'. $instance .'> }';
	    $rs = $this->query($r);
	    if (!empty($rs['results']['rows'])) {
	    	foreach( $rs['result']['rows'] as $row){
	    		if($row['s type'] == 'uri'){
					$objectValue = $this->getTermLink($row['s']);
				} elseif($row['s type'] == 'iri'){
					$objectValue = $this->getTermLink($row['s']);  //For older versions of ARC
				} elseif ($row['s type'] == 'literal'){
					$objectValue = $row['s'];
					//$objectValue = $object['val'];  //For older versions of ARC
				} elseif ($row['s type'] == 'bnode'){
					$objectValue = $this->parseBlankNode($row, $row['s']);
					//$objectValue = $this->parseBlankNode($res, $object['val']);  //For older versions of ARC
				}
				$instanceInfo[$row['p']]=$objectValue;
			}
	    }

  		//serialize($instanceInfo);
  		return $instanceInfo;
	}

	function writeInstanceHTML($class){
		$instanceInfo = $this->getInstanceInformation($class);
		//serialize($classInfo);
		global $g;
		$r = '<div class="specterm" id="term_' . $this->getShortName($class) . '">' . "\n";
		$r .= "<h3>Instance: " . $this->getShortName($class) . "</h3>\n";
		//$r .= "<h3>Instance: " . $this->getPrefixedName($class) . "</h3>\n";   // For prefied name instead of short name
		if(!empty($instanceInfo['http://purl.org/dc/elements/1.1/description'])){
			foreach($instanceInfo['http://purl.org/dc/elements/1.1/description'] as $description){
				$r .= "<p>" . $description . "</p>\n";
			}
		}
		if(!empty($instanceInfo['http://www.w3.org/2000/01/rdf-schema#comment'])){
			foreach($instanceInfo['http://www.w3.org/2000/01/rdf-schema#comment'] as $comment){
				$r .= "<p>" . $comment . "</p>\n";
			}
		}
		$r .= "<table>\n";

		//Write labels
		$r .= $this->writeTripleBox($instanceInfo, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');

		//Write extra details
		foreach($instanceInfo as $predicate => $objectArray){
			$prefixedPredicate =$this->getPrefixedName($predicate);
			if(!in_array($prefixedPredicate, $g['omit'])){
				$r .= $this->writeTripleBox($instanceInfo, $predicate);
			}
		}
		$r .= "</table>\n";
		$r .= '<p style="float: right; font-size: small;">[<a href="#sec-glance">back to top</a>]</p>';
		$r .= "<br />\n</div>\n";
		return $r;
	}

	// Build HTML list of terms.
	function buildazlist($classes, $properties, $instances){
		global $g;
		$azlist = '<div class="sec-glance">';
		if(!empty($classes)){
			$azlist .= "<p><strong>Classes:</strong> ";
			foreach($classes as $class){
				$azlist .= $this->getTermLink($class);
				$azlist .= "\n";
			}
    		$azlist .= "\n</p>";
		}
		if(!empty($properties)){
			$azlist .= "<p><strong>Properties:</strong> ";
			foreach($properties as $property){
				$azlist .= $this->getTermLink($property['p']);
				$azlist .= " \n";
			}
    		$azlist .= "\n</p>";
		}
		if(!empty($instances)){
			$azlist .= "<p><strong>Instances:</strong> ";
			foreach($instances as $instance){
				$azlist .= $this->getTermLink($instance);
				$azlist .= " \n";
			}
    		$azlist .= "\n</p>";
		}

    $azlist .= "\n</div>\n";
    return $azlist;
	}

	//Main function, Everything starts here.
	function specgen($specloc, $template, $prefix, $instances="False", $saveFile="False", $omits){

		//Clear specgen graph incase of earlier use
		$this->query('DELETE FROM <http://example.com/specgen>');


		//Get model in to ARC if not there already
		$l = 'LOAD ' . $specloc . ' INTO <http://example.com/specgen>';
		$this->query($l);

//		//Store check
//		$q = 'SELECT ?s ?p ?o FROM <http://example.com/specgen> WHERE {?s ?p ?o . }';
//		$rs = $this->query($q);
//    	$query = $rs['result']['rows'];
//    	serialize ($query);

		// Set global variables
		$this->setGlobals($prefix, $omits);

		// Generate HTML for Classes
		$classes = $this->getClasses();
		$classesHTML = "";
		foreach($classes as $class){
			$classesHTML .= $this->writeClassHTML($class);
		}


		// Generate HTML for Properties
		$properties = $this->getProperties();
		$propertyHTML = "";
		foreach($properties as $property){
			$propertyHTML .= $this->writePropertyHTML($property);
		}


		// Generate HTML for Instances
		if($instances == "True"){
			$instances = $this->getInstances();
			$instanceHTML = "";
			foreach($instances as $instance){
				$instanceHTML .= $this->writeInstanceHTML($instance);
			}
		} else {
			$instances = "";
			$instanceHTML = "";
		}

		// Build HTML list of terms.
		$azlistHTML = $this->buildazlist($classes, $properties, $instances);

		// Open the template doc and write the generated html,
		// output written to screen (default) or to specified file.


		if (($templateHandle = fopen($template, 'r')) === FALSE) {
        	exit("Failed to open template file\n");
      	} else {
        	$templateString = fread($templateHandle, filesize($template));
			fclose($templateHandle);

			// Write output text from template file and HTML fragments
			list($startMatter, $middleMatter, $endMatter, $check) = split('%s', $templateString);
			if($check != ""){
				exit('Temlate not in correct format. This works with the assumtpion that all "%" in the template are escaped to "%%" and it
				      contains two instance of "%s", the first for the at a glance list and the second for the full detail');
			} else {
				$output = $startMatter;
				$output .= $azlistHTML;
				$output .= $middleMatter;
				$output .= $classesHTML;
				$output .= $propertyHTML;
				$output .= $instanceHTML;
				$output .= $endMatter;

			}

      		// Write document, either to screen (default) or destination specified
			if ($saveFile == "False"){
				echo $output;
			} else {
				$saveHandle = fopen($saveFile, 'w');
        		fwrite($saveHandle, $output);
        		fclose($saveHandle);
        		echo "File $saveFile saved";
			}
		}
	}
}

