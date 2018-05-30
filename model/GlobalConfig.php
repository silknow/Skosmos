<?php

EasyRdf\RdfNamespace::set('skosmos', 'http://purl.org/net/skosmos#');
EasyRdf\RdfNamespace::set('skosext', 'http://purl.org/finnonto/schema/skosext#');
EasyRdf\RdfNamespace::set('isothes', 'http://purl.org/iso25964/skos-thes#');
EasyRdf\RdfNamespace::set('mads', 'http://www.loc.gov/mads/rdf/v1#');
EasyRdf\RdfNamespace::set('wd', 'http://www.wikidata.org/entity/');
EasyRdf\RdfNamespace::set('wdt', 'http://www.wikidata.org/prop/direct/');

/**
 * GlobalConfig provides access to the Skosmos configuration in config.ttl.
 */
class GlobalConfig extends DataObject {

    public function __construct($config_name='/../config.ttl')
    {
        try {
            $file_path = dirname(__FILE__) . $config_name;
            if (!file_exists($file_path)) {
                throw new Exception('config.ttl file is missing, please provide one.');
            }
            $this->parseConfig($file_path);

            $configResources = $this->graph->allOfType("skosmos:Configuration");
            if (is_null($configResources) || !is_array($configResources) || count($configResources) !== 1) {
                throw new Exception("config.ttl must have exactly one skosmos:Configuration");
            }
            $this->resource = $configResources[0];
            var_dump($this->getDefaultEndpoint());die;
            var_dump($configs);die;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return;
        }
    }

    /**
     * Parses configuration from the config.ttl file
     * @param string $filename path to config.ttl file
     */
    private function parseConfig($filename)
    {
        $this->graph = new EasyRdf\Graph();
        $parser = new SkosmosTurtleParser();
        $parser->parse($this->graph, file_get_contents($filename), 'turtle', $filename);
        $this->namespaces = $parser->getNamespaces();
    }

        /**
     * Returns a boolean value based on a literal value from the vocabularies.ttl configuration.
     * @param string $property the property to query
     * @param boolean $default the default value if the value is not set in configuration
     */
    private function getBoolean($property, $default = false)
    {
        $val = $this->resource->getLiteral($property);
        if ($val) {
            return filter_var($val->getValue(), FILTER_VALIDATE_BOOLEAN);
        }

        return $default;
    }

    /**
     * Returns an array of URIs based on a property from the vocabularies.ttl configuration.
     * @param string $property the property to query
     * @return string[] List of URIs
     */
    private function getResources($property)
    {
        $resources = $this->resource->allResources($property);
        $ret = array();
        foreach ($resources as $res) {
            $ret[] = $res->getURI();
        }

        return $ret;
    }

    /**
     * Returns a boolean value based on a literal value from the vocabularies.ttl configuration.
     * @param string $property the property to query
     * @param string $default default value
     * @param string $lang preferred language for the literal
     */
    private function getLiteral($property, $default=null, $lang=null)
    {
        if (!isset($lang)) {;
            $lang = $this->getEnvLang();
        }

        $literal = $this->resource->getLiteral($property, $lang);
        if ($literal) {
            return $literal->getValue();
        }

        // not found with selected language, try any language
        $literal = $this->resource->getLiteral($property);
        if ($literal)
          return $literal->getValue();

        return $default;
    }

    private function getConstant($name, $default)
    {
        if (defined($name) && constant($name)) {
            return constant($name);
        }
        return $default;
    }

    /**
     * Returns the UI languages specified in the configuration or defaults to
     * only show English
     * @return array
     */
    public function getLanguages()
    {
        return array('en' => 'en_GB.utf8');
    }

    /**
     * Returns the vocabulary configuration file specified the configuration
     * or vocabularies.ttl if not found.
     * @return string
     */
    public function getVocabularyConfigFile()
    {
        return $this->getConstant('VOCABULARIES_FILE', 'vocabularies.ttl');
    }

    /**
     * Returns the external HTTP request timeout in seconds or the default value
     * of 5 seconds if not specified in the configuration.
     * @return integer
     */
    public function getHttpTimeout()
    {
        return $this->getConstant('HTTP_TIMEOUT', 5);
    }

    /**
     * Returns the SPARQL HTTP request timeout in seconds or the default value
     * of 20 seconds if not specified in the configuration.
     * @return integer
     */
    public function getSparqlTimeout()
    {
        return $this->getConstant('SPARQL_TIMEOUT', 20);
    }

    /**
     * Returns the sparql endpoint address defined in the configuration. If
     * not then defaulting to http://localhost:3030/ds/sparql
     * @return string
     */
    public function getDefaultEndpoint()
    {
        return $this->getLiteral('skosmos:sparqlEndpoint', 'http://localhost:3030/ds/sparql');
    }

    /**
     * @return string
     */
    public function getSparqlGraphStore()
    {
        return $this->getConstant('SPARQL_GRAPH_STORE', null);
    }

    /**
     * Returns the maximum number of items to return in transitive queries if defined
     * in the configuration or the default value of 1000.
     * @return integer
     */
    public function getDefaultTransitiveLimit()
    {
        return $this->getConstant('DEFAULT_TRANSITIVE_LIMIT', 1000);
    }

    /**
     * Returns the maximum number of items to load at a time if defined
     * in the configuration or the default value of 20.
     * @return integer
     */
    public function getSearchResultsSize()
    {
        return $this->getConstant('SEARCH_RESULTS_SIZE', 20);
    }

    /**
     * Returns the configured location for the twig template cache and if not
     * defined defaults to "/tmp/skosmos-template-cache"
     * @return string
     */
    public function getTemplateCache()
    {
        return $this->getConstant('TEMPLATE_CACHE', '/tmp/skosmos-template-cache');
    }

    /**
     * Returns the defined sparql-query extension eg. "JenaText" or
     * if not defined falling back to SPARQL 1.1
     * @return string
     */
    public function getDefaultSparqlDialect()
    {
        return $this->getConstant('DEFAULT_SPARQL_DIALECT', 'Generic');
    }

    /**
     * Returns the feedback address defined in the configuration.
     * @return string
     */
    public function getFeedbackAddress()
    {
        return $this->getConstant('FEEDBACK_ADDRESS', null);
    }

    /**
     * Returns the feedback sender address defined in the configuration.
     * @return string
     */
    public function getFeedbackSender()
    {
        return $this->getConstant('FEEDBACK_SENDER', null);
    }

    /**
     * Returns the feedback envelope sender address defined in the configuration.
     * @return string
     */
    public function getFeedbackEnvelopeSender()
    {
        return $this->getConstant('FEEDBACK_ENVELOPE_SENDER', null);
    }

    /**
     * Returns true if exception logging has been configured.
     * @return boolean
     */
    public function getLogCaughtExceptions()
    {
        return $this->getConstant('LOG_CAUGHT_EXCEPTIONS', FALSE);
    }

    /**
     * Returns true if browser console logging has been enabled,
     * @return boolean
     */
    public function getLoggingBrowserConsole()
    {
        return $this->getConstant('LOG_BROWSER_CONSOLE', FALSE);
    }

    /**
     * Returns the name of a log file if configured, or NULL otherwise.
     * @return string
     */
    public function getLoggingFilename()
    {
        return $this->getConstant('LOG_FILE_NAME', null);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->getConstant('SERVICE_NAME', 'Skosmos');
    }

    /**
     * @return string
     */
    public function getServiceTagline()
    {
        return $this->getConstant('SERVICE_TAGLINE', null);
    }

    /**
     * @return string
     */
    public function getCustomCss()
    {
        return $this->getConstant('CUSTOM_CSS', null);
    }

    /**
     * @return boolean
     */
    public function getUiLanguageDropdown()
    {
        return $this->getConstant('UI_LANGUAGE_DROPDOWN', FALSE);
    }

    /**
     * @return string
     */
    public function getBaseHref()
    {
        return $this->getConstant('BASE_HREF', null);
    }

    /**
     * @return string
     */
    public function getGlobalPlugins()
    {
        return explode(' ', $this->getConstant('GLOBAL_PLUGINS', null));
    }

    /**
     * @return boolean
     */
    public function getHoneypotEnabled()
    {
        return $this->getConstant('UI_HONEYPOT_ENABLED', TRUE);
    }

    /**
     * @return integer
     */
    public function getHoneypotTime()
    {
        return $this->getConstant('UI_HONEYPOT_TIME', 5);
    }

    /**
     * @return boolean
     */
    public function getCollationEnabled()
    {
        return $this->getConstant('SPARQL_COLLATION_ENABLED', FALSE);
    }
}
