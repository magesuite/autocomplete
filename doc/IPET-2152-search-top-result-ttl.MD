## Performance improvement for getting top search result collection
Feature overwritten method:
```php
\Magento\Search\Model\ResourceModel\Query\Collection::isTopSearchResult
```
by plugin:
```php
\MageSuite\Autocomplete\Plugin\Search\Model\ResourceModel\Query\Collection\GetFromCacheIsTopSearchResult
```

### Default cache for top search result is disabled.
If you want to change it follow to:

1. Store
2. Configuration
3. Catalog
4. Catalog Search
5. Top Search Result Cache's Lifetime

### Default value of TTL is 3600s (1h).
If you want to change it follow to:

1. Store
2. Configuration
3. Catalog
4. Catalog Search
5. Top Search Result Cache's Lifetime (TTL)
