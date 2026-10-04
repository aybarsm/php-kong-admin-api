---
title: Bot Detection Plugin Configuration Reference
description: Detect and block bots or custom clients
url: "/plugins/bot-detection/reference/"
canonical_url: "/plugins/bot-detection/reference/"
content_type: reference
min_version:
  gateway: '1.0'
products:
- Kong Gateway
tools:
- deck
- Admin API
- Konnect API
- KIC
- Operator
- Terraform
tags:
- security
canonical: true
works_on:
- on-prem
- konnect


---

# Bot Detection Plugin Configuration Reference










```json
{
  "properties": {
    "config": {
      "properties": {
        "allow": {
          "default": [],
          "description": "An array of regular expressions that should be allowed. The regular expressions will be checked against the `User-Agent` header.",
          "items": {
            "type": "string"
          },
          "type": "array"
        },
        "deny": {
          "default": [],
          "description": "An array of regular expressions that should be denied. The regular expressions will be checked against the `User-Agent` header.",
          "items": {
            "type": "string"
          },
          "type": "array"
        }
      },
      "type": "object"
    },
    "expressions": {
      "additionalProperties": true,
      "type": "object"
    },
    "protocols": {
      "default": [
        "grpc",
        "grpcs",
        "http",
        "https"
      ],
      "description": "A set of strings representing HTTP protocols.",
      "items": {
        "enum": [
          "grpc",
          "grpcs",
          "http",
          "https"
        ],
        "type": "string"
      },
      "type": "array"
    },
    "route": {
      "additionalProperties": false,
      "description": "If set, the plugin will only activate when receiving requests via the specified route. Leave unset for the plugin to activate regardless of the route being used.",
      "properties": {
        "id": {
          "type": "string"
        }
      },
      "type": "object"
    },
    "service": {
      "additionalProperties": false,
      "description": "If set, the plugin will only activate when receiving requests via one of the routes belonging to the specified Service. Leave unset for the plugin to activate regardless of the Service being matched.",
      "properties": {
        "id": {
          "type": "string"
        }
      },
      "type": "object"
    }
  }
}
```

