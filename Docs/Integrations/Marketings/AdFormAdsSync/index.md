# AdForm sync integration
Integration will be work with paired market - advertiser.
Each market - another advertiser.
Some info by integration:
https://api.adform.com/help/guides/how-to-report-on-campaigns/reporting-stats
### Prepare accounts
1. Take client id and secret from ad form support (on creating api app)
2. Getting advertisers ids from https://www.adform.com/advertisermanagement (open each advertiser and get id from url https://www.adform.com/advertisermanagement/edit/{id})
3. Create "marketing_ad_form.json" file on the root application folder, fill them like example, one item - one account, client id and secret may be duplicated with another advertiser id
```json
[
    {
        "timezone": "UTC",
        "client_id": "reports.skyup.no@clients.adform.com",
        "client_secret": "d0Fui2YkDRA3FmmbfR5d4fX",
        "advertiser_id": "2080968",
        "currency_id": 2,
        "market_remote_id": 4,
        "marketing_channel_id": 3
    },
    ...
]

```
