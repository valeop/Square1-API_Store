# Square1 API Store Endpoints Guide
This README file will explain to you how to test all the endpoints related to the RESTful API made by laravel in phpStorm.

## Important information
- There are only 3 seeders:
   - **UserSedeer:** this seeder will create 15 new users; besides, automatically each user will have just **one** shopping cart because of the relation 1 by 1 between User and ShoppingCart models. It means that tables **users** and **shopping_carts** keep the same quantity of registers.
   - **ProductSeeder:** this seeder will create 20 new products.
   - **ProductVariantSeeder:** this seeder will create 15 new variants for products.
- Some models have relation rules or conditions to inject data and test some endpoints, that's why having factories only for User, ShoppingCart, Product, and ProductVariant models could be a better option; however, if yo want to inject data manually from factories, these are some commands you can use after call `php artisan tinker` in your project's terminal:

**NOTE:** these commands are not part of the guide, it's just additional information in case it can be useful for you.
  
   - **Create users:** `$users = App\Models\User::factory(*quantity*)->create()`
   - **Create shopping cart:** `$users->map(fn($user) => App\Models\ShoppingCart::factory()->create(['user_id' => $user->id])); ` 
   - **Create products:** `App\Models\Product::factory(*quantity*)->create()`
   - **Create product variants:** `App\Models\ProductVariant::factory(*quantity*)->create()`
   - **Create orders:** `App\Models\Order::factory(*quantity*)->create()`
   - **Create orders item:** `App\Models\OrderItem::factory(*quantity*)->create()`
   - **Create cart items:** `App\Models\CartItem::factory(*quantity*)->create()`     

## Endpoints Guide (explained)

Here is a public workspace on Postman which has an organized collection with packages that contain all the endpoints described in this guide: [Postman - API endpoints](https://www.postman.com/lunar-module-cosmologist-74614115/workspace/public/collection/33250817-b354591a-9922-41bb-9f07-44882a17d1ca?action=share&creator=33250817)

1. **Send this command in project's terminal:** php artisan serve
2. **Send this command to create data by seeders mentioned:** `php artisan db:seed`
3. Let's test the endpoints showed below:
   ### 1. For Product model
   - **GET | Products pagination:** `http://127.0.0.1:8000/api/v1/products?per_page=5&page=1`
   - **GET | All products:** `http://127.0.0.1:8000/api/v1/products`
   - **GET | Product by id:** `http://127.0.0.1:8000/api/v1/products/15` If "id" does not exist, it will return an error JSON report
   - **GET | Search products by attributes:**
     - `http://127.0.0.1:8000/api/v1/products/search?attributes=brand&value=calvin klein` (search by brand)
     - `http://127.0.0.1:8000/api/v1/products/search?attributes=collection&value=sq1` (search by collection)
     - `http://127.0.0.1:8000/api/v1/products/search?attributes=gender&value=FemaLe` (search by gender)
   - **GET | Search products by color:** `http://127.0.0.1:8000/api/v1/products/search?color=blue`
   - **GET | Search products by name:** `http://127.0.0.1:8000/api/v1/products/search?name=fit`
   - **GET | Search products by price:** `http://127.0.0.1:8000/api/v1/products/search?name=shirt&min_price=20&max_price=200`
   - **GET | Search products by size:** `http://127.0.0.1:8000/api/v1/products/search?size=s`
   - **POST | Save a new product:** `http://127.0.0.1:8000/api/v1/products`
     - There's two ways to send the JSON body, with key variants or without it:
       1. **key variants on it**
          
          ```
          {
             "name":"Hoodie",
             "description":"This is a POST test - hoodie",
             "price":"80",
             "other_attributes": {
                "gender":"Men",
                "brand":"Nike",
                "collection":"New Arrivals"
             },
             "variants": [
                {
                   "color": "Black",
                   "size": "M",
                   "stock_quantity": 10
                },
                {
                   "color": "Red",
                   "size": "M",
                   "stock_quantity": 10
                }
             ]
          }
          ```
          
       3. **without key variants**
          
          ```
          {
             "name":"Hoodie",
                "description":"This is a POST test - hoodie",
                "price":"80",
                "other_attributes": {
                    "gender":"Men",
                    "brand":"Nike",
                    "collection":"New Arrivals"
             }
          }
          ```

   - **PUT | Update a product key:** `http://127.0.0.1:8000/api/v1/products/2`
     - **JSON body**
       
       ```
       {
          "description":"I just changed the description and price attributes",
          "price":"50"
       }
       ```

   - **DELETE | Product:** `http://127.0.0.1:8000/api/v1/products/14`   
   ### 2. For User model (Auth)
   - **POST | Register new user:** `http://127.0.0.1:8000/api/v1/register`
     - **JSON body**
       
       This endpoint will return the user created info and a shopping cart register with "status: inactive" because the user was created, but no logged.
       
       ```
       {
          "name":"Galantis",
          "email":"galantis@gmail.com",
          "password":"password"
       }
       ```

   - **POST | Login user:** `http://127.0.0.1:8000/api/v1/login`
     - **JSON body**
     
       This endpoint will return the user info, the authentication token and a "shopping_cart_status: active" because the user is logged.

       ```
       {
          "email":"galantis@gmail.com",
          "password":"password"
       }
       ```

   - **POST | Logout user:** `http://127.0.0.1:8000/api/v1/logout` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman)
   - **GET | User profile:** `http://127.0.0.1:8000/api/v1/profile` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman)
   ### 3. For Order model
   - **GET | User orders:** `http://127.0.0.1:8000/api/v1/orders/` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman)
   - **GET | User order by id:** `http://127.0.0.1:8000/api/v1/orders/1` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman). If there's not an order with the id, it will return an error 404 Not Found.
   - **POST | Create a new order:** `http://127.0.0.1:8000/api/v1/orders/create` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman).
     - **JSON body**

       - This endpoint will return the order created with its items, and a "shopping_cart_status: completed" because the products in shopping cart were ordered.
       - If there are not items in shopping cart, it will return a JSON report and won't create any order.
       - In the body is not necessary to create any OrderItem key because every data (product_variant_id, quantity, price) is automatically extracted from CartItem model and added as an OrderItem that belongs to the order that will be created.
       - Besides, this endpoint deletes items associated to the current user in shopping cart after creating the order.

       ```
       {
          "date":"2020-05-15",
          "status":"delivered",
          "payment_method":"debit card",
          "shipping_address":"cl 60 #30"
       }
       ```

   ### 4. For ProductVariant model
   - **GET | Search variants by attributes:**
     - `http://127.0.0.1:8000/api/v1/variants/search?attributes=brand&value=prada` (search by brand)
     - `http://127.0.0.1:8000/api/v1/variants/search?attributes=collection&value=NEW ARRIVALS` (search by collection)
     - `http://127.0.0.1:8000/api/v1/variants/search?attributes=gender&value=Male` (search by gender)
   - **GET | Search variants by color:** `http://127.0.0.1:8000/api/v1/variants/search?color=black`
   - **GET | Search variants by name:** `http://127.0.0.1:8000/api/v1/variants/search?name=glove`
   - **GET | Search variants by price:** `http://127.0.0.1:8000/api/v1/variants/search?min_price=51&max_price=300`
   - **GET | Search variants by size:** `http://127.0.0.1:8000/api/v1/variants/search?size=xL`
   - **GET | All variants:** `http://127.0.0.1:8000/api/v1/variants`
   - **GET | Variant by id:** `http://127.0.0.1:8000/api/v1/variants/11` If id does not exist, it will return an error JSON report
   - **POST | Save a new variant:** `http://127.0.0.1:8000/api/v1/variants`
     - **JSON body**
       
       ```
       {
          "product_id":"5",
          "color":"blue",
          "size":"S",
          "stock_quantity":"5"
       }
       ```
   - **DELETE | Variant:** `http://127.0.0.1:8000/api/v1/variants/10` If id does not exist, it will return an error JSON report
   ### 5. For ShoppingCart model
   - **GET | Cart items:** `http://127.0.0.1:8000/api/v1/cart` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman).
   - **POST | Add item to shopping cart:** `http://127.0.0.1:8000/api/v1/cart/add` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman).
     - **JSON body**
       
       This body doesn't require a price because it is extracted from Product associated to the variant id in the JSON
       
       ```
       {
          "cart_items": [
              {
                  "product_variant_id": "2",
                  "quantity": 2
              }
          ]
       }
       ```

   - **PUT | Update item quantity in shopping cart:** `http://127.0.0.1:8000/api/v1/cart/update/1` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman).
     - **JSON body**
       
       ```
       {
          "quantity": 4
       }
       ```

   - **DELETE | Item from shopping cart:** `http://127.0.0.1:8000/api/v1/cart/remove/1` This endpoint needs the token provided (Used in Bearer Token Auth method on Postman). 
     
