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
        if (isset($result[\MageSuite\Autocomplete\Model\Autocomplete\SuggestedPhrasesProvider::AUTOCOMPLETE_FIELD])) {
            $result[\MageSuite\Autocomplete\Model\Autocomplete\SuggestedPhrasesProvider::AUTOCOMPLETE_FIELD]['analyzer'] = 'standard';
            $result[\MageSuite\Autocomplete\Model\Autocomplete\SuggestedPhrasesProvider::AUTOCOMPLETE_FIELD]['search_analyzer'] = 'standard';
        }

        return $result;
    }
}
