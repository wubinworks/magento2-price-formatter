# Price Formatter for Magento 2
**Formatting Price has never been easier with this extension.**
<br /><br />
<a href="https://raw.githubusercontent.com/wubinworks/home/master/images/Wubinworks/PriceFormatter/price-formatter.png" target="_blank"><img src="https://raw.githubusercontent.com/wubinworks/home/master/images/Wubinworks/PriceFormatter/price-formatter.png" alt="Wubinworks Price Formatter" title="Wubinworks Price Formatter"/></a>

 - Can use HTML and support "per Store View" configuration
 - Placeholders: `{{price}}`, `{{price_no_decimal}}`, `{{price_bitcoin}}`, `{{symbol}}`
 - For `{{symbol}}`, Check `Stores > Currency > Currency Symbols`
 - Example: &#x91d1;{{price_no_decimal}}&#x5186;
 - Decimal places:
   - {{price}} => 2
   - {{price_no_decimal}} => 0
   - {{price_bitcoin}} => 8
<br /><br />

 - HTML使用可。ストアごとに設定可
 - プレースホルダー: `{{price}}`, `{{price_no_decimal}}`, `{{price_bitcoin}}`, `{{symbol}}`
 - `{{symbol}}`は`店舗 > 通貨 > 通貨記号`を参照
 - 例: 金{{price_no_decimal}}円
 - 小数位:
   - {{price}} => 2
   - {{price_no_decimal}} => 0
   - {{price_bitcoin}} => 8

# How to use
Read the description above and go to backend `Stores > Configuration > Currency Setup > Currency Options > Price Format`

上を読んで、バックエンドの`店舗 > 設定 > 通貨セットアップ > 通貨オプション > Price Format`へ

# Requirements
**Magento 2.4**

# Installation
**`composer require wubinworks/module-price-formatter`**

# For developers
The format you set in `Price Format` affects method `\Magento\Framework\Pricing\Helper\Data::currency` in `frontend area`.
