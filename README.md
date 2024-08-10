# Laravel 10, Swagger, dan JWT Integration

## Langkah-langkah

### 1. Buat Migration dan Seeder

#### 1.1. Buat Migration untuk Tabel `users`, `category_products`, dan `products`

Jalankan perintah berikut untuk membuat migration:

```bash
php artisan make:migration create_users_table
php artisan make:migration create_category_products_table
php artisan make:migration create_products_table
```

Kemudian, edit file migration yang telah dibuat di `database/migrations`:

- Migration: `create_users_table.php`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
```
- Migration: `create_category_products_table.php`
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_category_id')->constrained('category_products');
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->string('image')->nullable();
    $table->timestamps();
});
```
- Migration: `create_products_table.php`
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_category_id')->constrained('category_products');
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->string('image')->nullable();
    $table->timestamps();
});
```

Jalankan migration:
```bash 
php artisan migrate
```
##### 1.2. Buat Seeder untuk Mengisi Data Awal

- Jalankan perintah untuk membuat seeder:
```bash
php artisan make:seeder CategoryProductsSeeder
```
- Edit file seeder di `database/seeders/CategoryProductsSeeder.php`:

```php
use App\Models\CategoryProduct;

CategoryProduct::create(['name' => 'Elektronik']);
CategoryProduct::create(['name' => 'Pakaian']);
```
- Jalankan seeder:

```bash
php artisan db:seed --class=CategoryProductsSeeder
```
### 2. Buat API dengan JWT

##### 2.1. Instalasi Package JWT

- Instalasi package JWT dengan menjalankan perintah:

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```
#### 2.2. Konfigurasi auth.php

- Edit file config/auth.php untuk mengubah driver menjadi JWT:

```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

#### 2.3. Buat Controller dan Route

- Buat controller untuk authentication dan product:

```bash
php artisan make:controller AuthController
php artisan make:controller ProductController
php artisan make:controller CategoryProductController
```
- Controller: `AuthController.php`

```php
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

public function register(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'User successfully registered',
        'user' => $user,
    ], 201);
}

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!$token = Auth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
}

protected function respondWithToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
    ]);
}
```

- Controller: `ProductController.php`

```php

use App\Models\Product;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

public function index()
{
    $products = Product::all();
    return response()->json($products);
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'price' => 'required|numeric',
        'product_category_id' => 'required|exists:category_products,id',
    ]);

    $product = Product::create($request->all());

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,
    ]);
}
```

- Tambahkan route di routes/api.php:

```php

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('products', ProductController::class);
    Route::resource('category-products', CategoryProductController::class);
});
```
### 3. Buat Dokumentasi API dengan Swagger

- Instalasi Swagger dengan menjalankan perintah:

```bash

composer require "darkaonline/l5-swagger"
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

- Edit file `config/l5-swagger.php` untuk menyesuaikan konfigurasi.

Tambahkan komentar Swagger di controller untuk mendokumentasikan API, misalnya:

```php

/**
 * @OA\Post(
 *     path="/api/login",
 *     operationId="loginUser",
 *     tags={"Authentication"},
 *     summary="Login user",
 *     description="Login user with JWT",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="token_type", type="string"),
 *             @OA\Property(property="expires_in", type="integer"),
 *         ),
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 * )
 */
```

Untuk melihat dokumentasi, jalankan:

```bash
php artisan l5-swagger:generate
```
Kemudian akses dokumentasi Swagger di http://localhost:8000/api/documentation.