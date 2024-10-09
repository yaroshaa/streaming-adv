# STREAM
## [Docs](Docs/index.md)

### Orders API

To store orders send POST JSON to `/api/orders`:

    {
        "order_id": 71690,
        "updated_at": "2021-01-29T14:01:38.489Z",
        "status": {
          "id": 7,
          "name": "closed"
        },
        "products": [
          {
            "id": 2719,
            "name": "Monster Keto Protein Cookie 12x33g",
            "link": "https://www.tights.no/butikk/monster-keto-protein-cookie-12x33g/",
            "image_link": "https://www.tights.no/wp-content/uploads/sites/7/2020/12/Monstersnacks_Keto-Cookies_Box_View-01-1200x1500.png",
            "price": 610.175151507101,
            "profit": 101.66845844135776,
            "discount": 30.508757575355048,
            "qty": 2,
            "weight": 9.573106708094599
          },
          {
            "id": 2618,
            "name": "Monster Lavkarbo Zero Drops - Coconut - NY FORBEDRET OPPSKRIFT!",
            "link": "https://www.tights.no/butikk/monster-lavkarbo-zero-drops-coconut/",
            "image_link": "https://www.tights.no/wp-content/uploads/sites/7/2020/12/Monstersnacks_Zero-Drops_Coconut_View-02-1200x1500.png",
            "price": 507.1867910133615,
            "profit": 266.689231726082,
            "discount": 0,
            "qty": 1,
            "weight": 2.9843003665160284
          }
        ],
        "customer": {
          "id": 5832,
          "name": "Augustus Cox"
        },
        "address": "Amtmand Bangs gate 1A, 3019 Drammen, Norway",
        "currency": {
          "code": "EUR"
        },
        "market": {
          "id": 1,
          "name": "Tights.no"
        }
    }   
