# Performission Ads sync integration
Based on https://help.targetcircle.com/en/articles/1585346-transactions

1. Take api token from https://performission.targetcircle.com/advertiser/settings#/account
2. Getting offer ids from https://performission.targetcircle.com/advertiser/#/offers (SID column)
3. Create "marketing_performission.json" file on the root application folder, fill them like example, one item - one account, client id and secret may be duplicated with another advertiser id
```json
[
    {
        "timezone": "UTC",
        "api_token": "QdXvXBUtfEzeERqsSh8Wnce",
        "offer_sid": "pyo55f",
        "currency_id": 1,
        "market_remote_id": 4,
        "marketing_channel_id": 8
    }
    ...
]

```
