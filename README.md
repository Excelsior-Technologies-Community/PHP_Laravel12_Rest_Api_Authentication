# PHP_Laravel12_Rest_Api_Authentication

## Introduction
This project demonstrates **REST API Authentication in Laravel 12 using Laravel Sanctum** with a **complete, production-ready structure**.

It strictly follows the reference tutorial provided by your sir and additionally includes **real‑world database practices** such as:

- API Resources (Eloquent Resources)
- `status` column
- `created_by`, `updated_by`
- Soft Deletes
- Standardized API responses

---

## Project Features

- Laravel 12 REST API
- Sanctum Token Authentication
- User Register & Login APIs
- Protected Product CRUD APIs
- Eloquent API Resources
- Soft Delete support
- Clean & consistent JSON responses

---

## Requirements

- PHP >= 8.1
- Composer
- MySQL / MariaDB
- Laravel Installer
- Postman

---

## STEP 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Rest_Api_Authentication "12.*"
cd PHP_Laravel12_Rest_Api_Authentication
```

Verify version:

```bash
php artisan --version
```

---

##  STEP 2: Install Sanctum API 

In Laravel 12, by default, we don't have an api.php route file. So, you just need to run the following command to install Sanctum with api.php file.

```bash
php artisan install:api
```

---

## STEP 3: User Model Configuration

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method \Laravel\Sanctum\NewAccessToken createToken(string $name, array $abilities = ['*'], \DateTimeInterface|null $expiresAt = null)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * These fields can be filled using User::create() or $user->update().
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * These fields will not be included when converting the model to arrays or JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * Cast specific attributes to native types automatically.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converts email_verified_at to DateTime object
            'password' => 'hashed',  // Automatically hashes the password when set
        ];
    }
}
```

---

## STEP 4: Database Configuration

Update `.env`:

```env
DB_DATABASE=laravel12_restapi
DB_USERNAME=root
DB_PASSWORD=
```
Run migration: (Create Database)

```bash
php artisan migrate
```
---

## STEP 5: Product Migration & Model

```bash
php artisan make:model Product -m
```

### Migration
**File:** `database/migrations/xxxx_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method creates the 'products' table with all required columns.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key 'id' (auto-increment)
            $table->string('name'); // Product name
            $table->text('detail'); // Product details/description
            $table->boolean('status')->default(value: 1); // Product status: 1 = active, 0 = inactive
            $table->unsignedBigInteger('created_by')->nullable(); // User ID who created the product (nullable)
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the product (nullable)
            $table->softDeletes(); // Soft delete column 'deleted_at'
            $table->timestamps(); // 'created_at' and 'updated_at' timestamps
        });
    }

    /**
     * Reverse the migrations.
     * This method drops the 'products' table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run migration:

```bash
php artisan migrate
```

### Product Model
**File:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Product model represents the products in the application.
 * It supports soft deletes and can be mass assigned using $fillable.
 */
class Product extends Model
{
    // Use HasFactory for database factories
    // Use SoftDeletes to enable soft deleting of products
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * These fields can be set via Product::create() or $product->update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',        // Product name
        'detail',      // Product description/details
        'status',      // Product status: 1=active, 0=inactive
        'created_by',  // ID of user who created the product
        'updated_by'   // ID of user who last updated the product
    ];
}
```

---

## STEP 6: API Routes

**File:** `routes/api.php`

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;

// Default user route (commented out)
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| These routes are publicly accessible for registering and logging in users.
*/
Route::post('register',[RegisterController::class,'register']); // Register a new user
Route::post('login',[RegisterController::class,'login']);       // Login and get Sanctum API token

/*
|--------------------------------------------------------------------------
| Protected Product Routes
|--------------------------------------------------------------------------
| These routes are protected using Sanctum middleware.
| Only authenticated users with a valid API token can access them.
*/
Route::middleware('auth:sanctum')->group(function () {

    // List all products (GET)
    Route::get('/listProducts', [ProductController::class, 'listProducts']);

    // Add new product (POST)
    Route::post('/addProduct', [ProductController::class, 'createProduct']);

    // Show single product details (GET)
    Route::get('/showProduct/{id}', [ProductController::class, 'showProduct']);

    // Update product details (POST)
    Route::post('/updateProduct/{id}', [ProductController::class, 'updateProduct']);

    // Delete product (Soft Delete) (POST)
    Route::post('/deleteProduct/{id}', [ProductController::class, 'deleteProduct']);
});
```

---

## STEP 7: Base API Controller

```bash
php artisan make:controller API/BaseController
```

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * BaseController provides common API response methods.
 * Other API controllers can extend this class to use standardized responses.
 */
class BaseController extends Controller
{
    /**
     * Send a successful JSON response.
     *
     * @param mixed $data    The data to return in the response
     * @param string $message  A message describing the response
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($data, $message)
    {
        return response()->json([
            'success' => true,  // Indicates success
            'data' => $data,    // Response data
            'message' => $message // Informational message
        ], 200); // HTTP status code 200 OK
    }

    /**
     * Send an error JSON response.
     *
     * @param string $message  The error message
     * @param array $errors    Optional array of detailed errors
     * @param int $code        HTTP status code (default 401 Unauthorized)
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($message, $errors = [], $code = 401)
    {
        return response()->json([
            'success' => false, // Indicates failure
            'message' => $message, // Error message
            'errors' => $errors   // Optional detailed errors
        ], $code); // HTTP status code
    }
}
```

---

## STEP 8: Register & Login Controller

```bash
php artisan make:controller API/RegisterController
```

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;    
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * RegisterController handles user registration and login for the API.
 * It extends BaseController to use standardized API response methods.
 */
class RegisterController extends BaseController
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',                    // Name is required
            'email' => 'required|email|unique:users', // Must be unique email
            'password' => 'required|confirmed'      // Password must be confirmed
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encrypt password
        ]);

        // Generate Sanctum API token
        $token = $user->createToken('API Token')->plainTextToken;

        // Return success response with token and user name
        return $this->sendResponse([
            'token' => $token,
            'name' => $user->name
        ], 'User registered successfully');
    }

    /**
     * Login existing user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Attempt to authenticate user with email and password
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Generate Sanctum API token
            $token = $user->createToken('API Token')->plainTextToken;

            // Return success response with token and user name
            return $this->sendResponse([
                'token' => $token,
                'name' => $user->name
            ], 'User logged in successfully');
        }

        // Return error response if authentication fails
        return $this->sendError('Unauthorized', ['error' => 'Invalid credentials']);
    }
}
```

---

## STEP 9: API Resource (IMPORTANT)

```bash
php artisan make:resource ProductResource
```

**File:** `app/Http/Resources/ProductResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductResource transforms the Product model data into a standardized JSON format.
 * This ensures consistent API responses for products.
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,                        // Product ID
            'name' => $this->name,                    // Product name
            'detail' => $this->detail,                // Product description/details
            'status' => $this->status,                // Product status: 1=active, 0=inactive
            'created_by' => $this->created_by,        // ID of user who created the product
            'updated_by' => $this->updated_by,        // ID of user who last updated the product
            'created_at' => $this->created_at?->format('d/m/Y'), // Created date formatted
            'updated_at' => $this->updated_at?->format('d/m/Y'), // Updated date formatted
        ];
    }
}
```

---

## STEP 10: Product Controller

```bash
php artisan make:controller API/ProductController --resource
```

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;

/**
 * ProductController handles all Product-related API operations.
 * This includes listing, creating, showing, updating, and soft deleting products.
 * It extends BaseController to use standardized API response methods.
 */
class ProductController extends BaseController
{
    /**
     * List all active products.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function listProducts()
    {
        // Fetch only products with status = 1 (active)
        $products = Product::where('status', 1)->get();

        // Return success response with product collection
        return $this->sendResponse(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    /**
     * Create a new product.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(Request $request)
    {
        // Create a new product record
        $product = Product::create([
            'name'       => $request->name,          // Product name
            'detail'     => $request->detail,        // Product detail/description
            'status'     => 1,                        // Default status = active
            'created_by' => Auth::id(),              // ID of the logged-in user creating the product
        ]);

        // Return success response with the newly created product
        return $this->sendResponse(
            new ProductResource($product),
            'Product created successfully'
        );
    }

    /**
     * Show details of a single product.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProduct($id)
    {
        // Find product by ID
        $product = Product::find($id);

        // Return error if product not found
        if (!$product) {
            return $this->sendError('Product not found');
        }

        // Return success response with product details
        return $this->sendResponse(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * Update an existing product.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(Request $request, $id)
    {
        // Find product by ID
        $product = Product::find($id);

        // Return error if product not found
        if (!$product) {
            return $this->sendError('Product not found');
        }

        // Update product details
        $product->update([
            'name'        => $request->name,          // Updated product name
            'detail'      => $request->detail,        // Updated product detail
            'updated_by'  => Auth::id(),             // ID of logged-in user updating the product
        ]);

        // Return success response with updated product
        return $this->sendResponse(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Soft delete a product.
     * Also sets status = 0 before deletion.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct($id)
    {
        // Find product by ID
        $product = Product::find($id);

        // Return error if product not found
        if (!$product) {
            return $this->sendError('Product not found');
        }

        // Set status to 0 before soft delete
        $product->update([
            'status' => 0,             // Mark product as inactive
            'updated_by' => Auth::id(),// ID of user performing deletion
        ]);

        // Perform soft delete
        $product->delete();

        // Return success response
        return $this->sendResponse([], 'Product deleted successfully');
    }
}
```

---

## STEP 11: Run Project

```bash
php artisan serve
```

---

## 🔗 API Endpoints

| Method | Endpoint | Description |
|--------|---------|-------------|
| POST   | /api/register           | Register a new user |
| POST   | /api/login              | Login user and get token |
| GET    | /api/listProducts       | List all active products |
| POST   | /api/addProduct         | Add a new product |
| GET    | /api/showProduct/{id}   | Show single product details |
| POST   | /api/updateProduct/{id} | Update existing product |
| POST   | /api/deleteProduct/{id} | Soft delete a product (status set to 0) |

**Authorization Header (for protected routes):**  

```
Bearer YOUR_API_TOKEN
```

---

## STEP 12: Postman Setup

#### 1) Register a new user

Method: POST

URL: 

```
http://127.0.0.1:8000/api/register
```
Body (form-data or JSON):

```
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

Response: JSON containing token and name.

---

#### 2) Login existing user

Method: POST

URL:

```
 http://127.0.0.1:8000/api/login
```
Body (JSON):

```
{
    "email": "john@example.com",
    "password": "password123"
}
```

Response: JSON with token for authorization.

---

#### 3) List all products

Method: GET

URL: 

```
http://127.0.0.1:8000/api/listProducts
```
Headers:

Authorization: Bearer YOUR_API_TOKEN


Response: JSON array of all active products.

---

#### 4) Add a new product

Method: POST

URL: 

```
http://127.0.0.1:8000/api/addProduct
```
Headers:

Authorization: Bearer YOUR_API_TOKEN


Body (JSON):

```
{
    "name": "Product 1",
    "detail": "This is a test product."
}
```

Response: JSON of newly created product.

---

#### 5) Show single product

Method: GET

URL: 

```
http://127.0.0.1:8000/api/showProduct/1
```
Headers:

Authorization: Bearer YOUR_API_TOKEN


Response: JSON of requested product.

---

#### 6) Update product

Method: POST

URL: 

```
http://127.0.0.1:8000/api/updateProduct/1
```

Headers:

Authorization: Bearer YOUR_API_TOKEN


Body (JSON):

```
{
    "name": "Updated Product",
    "detail": "Updated product details"
}
```

Response: JSON of updated product.

---

#### 7) Soft delete product

Method: POST

URL:
 
```
http://127.0.0.1:8000/api/deleteProduct/1
```

Headers:

Authorization: Bearer YOUR_API_TOKEN


Response: Success message. Product status becomes 0 and it is soft deleted.

---

## Project Structure

```
PHP_Laravel12_Rest_Api_Authentication/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/
│   │   │       ├── BaseController.php        # Standardized API responses
│   │   │       ├── RegisterController.php   # Register & Login APIs
│   │   │       └── ProductController.php    # Product CRUD APIs
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php                          # User model with Sanctum
│   │   └── Product.php                       # Product model with SoftDeletes
│   └── Resources/
│       └── ProductResource.php               # API resource for product formatting
│
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_users_table.php
│   │   └── xxxx_create_products_table.php
│   └── seeders/
│
├── routes/
│   └── api.php                                # API routes
│
├── .env                                      # Database configuration & Sanctum setup
├── composer.json
├── artisan
└── README.md                                  # Full explanation and setup instructions
```
---

## Output

---

**1) Register a new user**
   
<img width="1386" height="996" alt="Screenshot 2025-12-16 120358" src="https://github.com/user-attachments/assets/46778be2-bdef-4495-b750-7f6757c064a0" />

**2) Login existing user** 

<img width="1388" height="998" alt="Screenshot 2025-12-16 120522" src="https://github.com/user-attachments/assets/b1a3aa39-0823-4733-8de0-8455a9d20bde" />

**3) List all products**

<img width="1388" height="998" alt="Screenshot 2025-12-16 120522" src="https://github.com/user-attachments/assets/035fb26b-1832-4e76-ad87-66aad9e43a83" />

**4) Add a new product**

<img width="1381" height="1001" alt="Screenshot 2025-12-16 111029" src="https://github.com/user-attachments/assets/13b66af1-b56e-444b-a16b-9ab31b07da6e" />

**5) Show single product**

<img width="1381" height="1001" alt="Screenshot 2025-12-16 111029" src="https://github.com/user-attachments/assets/00613695-ed85-4db8-937c-f139b53a25e9" />

**6) Update product**

<img width="1381" height="1001" alt="Screenshot 2025-12-16 111029" src="https://github.com/user-attachments/assets/2a992aff-c94c-4f15-93cb-7fb02e3d8a0c" />

**7) Soft delete product**

<img width="1387" height="991" alt="Screenshot 2025-12-16 121109" src="https://github.com/user-attachments/assets/d645ed89-36d4-4528-97ff-0ea87dda2598" />


---
Your PHP_Laravel12_Rest_Api_Authentication Project is Now Ready!
