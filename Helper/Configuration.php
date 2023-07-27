<?php

namespace MageSuite\Autocomplete\Helper;

class Configuration
{
    const GENERATION_ENABLED_XML_PATH = 'smile_elasticsuite_autocomplete_settings/term_autocomplete/generate_terms';
    const BOLD_SEARCHED_TEXT_IN_RESULTS_ENABLED_XML_PATH = 'smile_elasticsuite_autocomplete_settings/term_autocomplete/bold_searched_text_in_results';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isGenerationEnabled() {
        return $this->scopeConfig->getValue(self::GENERATION_ENABLED_XML_PATH);
    }

    public function isBoldingOfSearchQueryEnabled() {
        return $this->scopeConfig->getValue(self::BOLD_SEARCHED_TEXT_IN_RESULTS_ENABLED_XML_PATH);
    }
}
