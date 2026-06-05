# Chapter 7: API Documentation

## Learning Objectives

- Document APIs with OpenAPI/Swagger
- Generate documentation automatically
- Write clear API references
- Test APIs with documentation

---

## 7.1 OpenAPI Specification

```yaml
openapi: 3.0.0
info:
  title: E-Commerce API
  version: 1.0.0
  description: RESTful API for e-commerce platform

servers:
  - url: https://api.example.com/v1

paths:
  /products:
    get:
      summary: List all products
      tags: [Products]
      parameters:
        - name: page
          in: query
          schema:
            type: integer
            default: 1
        - name: per_page
          in: query
          schema:
            type: integer
            default: 15
        - name: category
          in: query
          schema:
            type: string
        - name: sort
          in: query
          schema:
            type: string
            enum: [price, name, created_at]
      responses:
        '200':
          description: Paginated list of products
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/Product'
                  meta:
                    $ref: '#/components/schemas/PaginationMeta'

    post:
      summary: Create a new product
      tags: [Products]
      security:
        - bearerAuth: []
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/ProductInput'
      responses:
        '201':
          description: Product created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/Product'
        '422':
          description: Validation error

components:
  schemas:
    Product:
      type: object
      properties:
        id:
          type: integer
        name:
          type: string
        price:
          type: number
          format: float
        category:
          type: string
        created_at:
          type: string
          format: date-time

    PaginationMeta:
      type: object
      properties:
        current_page:
          type: integer
        per_page:
          type: integer
        total:
          type: integer
        last_page:
          type: integer

  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
```

---

## 7.2 Exercises

1. Document a complete REST API with OpenAPI 3.0
2. Generate interactive Swagger UI documentation
3. Add request/response examples to all endpoints
4. Create a Postman collection from the OpenAPI spec

---

## Further Reading

- **Doc:** [OpenAPI Specification](https://swagger.io/specification/)
- **Doc:** [Swagger PHP](https://zircote.github.io/swagger-php/)
