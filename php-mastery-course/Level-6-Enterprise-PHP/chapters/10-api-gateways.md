# Chapter 10: API Gateways (Kong, Traefik)

## Learning Objectives

- Understand API gateway patterns
- Configure Kong for PHP services
- Implement rate limiting and auth at gateway
- Handle routing and load balancing

---

## 10.1 Kong Gateway Configuration

```php
<?php
// Kong declarative config (kong.yml)
// _format_version: "3.0"
// services:
//   - name: user-service
//     url: http://user-service:8080
//     routes:
//       - name: user-routes
//         paths:
//           - /api/users
//         methods: [GET, POST, PUT, DELETE]
//         strip_path: false
//     plugins:
//       - name: key-auth
//       - name: rate-limiting
//         config:
//           minute: 60
//           hour: 1000
//
//   - name: order-service
//     url: http://order-service:8081
//     routes:
//       - name: order-routes
//         paths:
//           - /api/orders
//         methods: [GET, POST]
//     plugins:
//       - name: jwt-auth
//       - name: cors

// Custom Kong plugin in Lua
// plugins/my-rate-limiter/handler.lua
local MyRateLimiter = {
    VERSION = "1.0.0",
    PRIORITY = 1000,
}

function MyRateLimiter:access(conf)
    local consumer = kong.client.get_consumer()
    local key = consumer and consumer.id or kong.client.get_forwarded_ip()
    
    local current, err = kong.redis:get(key)
    if not current then
        kong.redis:set(key, 1)
        kong.redis:expire(key, conf.window)
        return
    end
    
    if tonumber(current) >= conf.limit then
        return kong.response.exit(429, {
            message = "Rate limit exceeded",
            retry_after = kong.redis:ttl(key),
        })
    end
    
    kong.redis:incr(key)
end

return MyRateLimiter
```

---

## 10.2 Exercises

1. Deploy Kong Gateway with Docker
2. Route multiple PHP microservices through Kong
3. Add authentication, rate limiting, and CORS at gateway level
4. Configure load balancing across service instances

---

## Further Reading

- **Doc:** [Kong Gateway](https://docs.konghq.com/gateway/)
- **Doc:** [Traefik](https://doc.traefik.io/traefik/)
