<?php

namespace MageSuite\Autocomplete\Helper;

class Configuration
{
    public const GENERATION_ENABLED_XML_PATH = 'smile_elasticsuite_autocomplete_settings/term_autocomplete/generate_terms';
    public const BOLD_SEARCHED_TEXT_IN_RESULTS_ENABLED_XML_PATH = 'smile_elasticsuite_autocomplete_settings/term_autocomplete/bold_searched_text_in_results';
    public const TOP_SEARCH_RESULT_CACHE_ENABLED_XML_PATH = 'catalog/search/top_search_result_cache_enabled';
    public const TOP_SEARCH_RESULT_CACHE_TTL_XML_PATH = 'catalog/search/top_search_result_cache_ttl';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isGenerationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::GENERATION_ENABLED_XML_PATH);
    }

    public function isBoldingOfSearchQueryEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::BOLD_SEARCHED_TEXT_IN_RESULTS_ENABLED_XML_PATH);
    }

    public function isEnabledTopSearchResultCache(): bool
    {
        return $this->scopeConfig->isSetFlag(self::TOP_SEARCH_RESULT_CACHE_ENABLED_XML_PATH);
    }

    public function getTopSearchResultCacheTTL(): int
    {
        return (int)$this->scopeConfig->getValue(self::TOP_SEARCH_RESULT_CACHE_TTL_XML_PATH);
    }
}
