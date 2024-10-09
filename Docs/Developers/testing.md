# Testing

## Prepare environments

#### HelpScout
* Configured HelpScout integration by [HelpScout Feedback integration](../Integrations/HelpScoutFeedback/index.md)
* For tests modules/Feedbacks/config/helpscout_config.json
```shell
{
    "1111111111111": 1, // that mailbox id only for testing application

    "244356": 1,

    "244154": 2

}
```

#### Facebook
* Configured Facebook integration by [Facebook Feedback integration](../Integrations/FacebookFeedback/index.md)
* For tests modules/Feedbacks/config/facebook_webhooks_config.json
```shell
{
    "page" : {
        "feedback_pages": {
            "537794966626992" : {
                "source_remote_id": 1,
                "market_remote_id": 1
            }
        }
    }
}

```
## Running
```shell
php artisan test

# Example run with filter by test method name
php artisan test --filter testCheckSignature 
```
