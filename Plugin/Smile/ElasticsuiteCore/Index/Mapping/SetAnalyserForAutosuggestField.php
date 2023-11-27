<?php

namespace MageSuite\Autocomplete\Plugin\Smile\ElasticsuiteCore\Index\Mapping;

/**
 * It is impossible to set analyzer for completion type of field in default ElasticSuite logic
 * Analyzer configuration has to be injected before it's passed to ElasticSearch
 */
class SetAnalyserForAutosuggestField
{
    public function afterGetProperties(\Smile\ElasticsuiteCore\Index\Mapping $subject, $result)
    {
        if (isset($result['autocomplete_suggest'])) {
            $result['autocomplete_suggest']['analyzer'] = 'standard';
            $result['autocomplete_suggest']['search_analyzer'] = 'standard';
        }

        return $result;
    }
}
