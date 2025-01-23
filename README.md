# Square1 API Store Endpoints Guide
This README file will explain to you how to prove all the endpoints related to the RESTful API made with laravel in phpStorm

1. Open Postman
2. write this command in the terminal project's location: php artisan serve
3. Let's test the below endpoints
   # Product: Model
   ~ GET - Products pagination: http://127.0.0.1:8000/api/v1/products?per_page=5&page=1
   ~ GET - Search products by color: http://127.0.0.1:8000/api/v1/products/search?color=blue
