<?php

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController /*as ApiAuthController */;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Middleware\IsAdmin;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::prefix()-> group(['middleware'=>['auth:sanctum']],function () {} //to implement prefix

// public Routes _____________________________________________________________________________

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// protected Routes (With Sanctum Auth) ______________________________________________________

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Years السنوات الدراسية
    //Route::resource('/years', ApiYearController::class)/*->only(['index', 'show'])*/;
    //Route::get('/seasons/seasons_by_year_id/{year_id}', [ApiSeasonController::class, 'index']);

    // Clients Management
    Route::prefix('clients')->group(function () {

        Route::get('/show-accepted-clients', [UserController::class, 'show_accepted_clients']);
        Route::get('/show-clients-requests', [UserController::class, 'show_clients_requests']);
        Route::get('/show-client-details/{client_id}', [UserController::class, 'show_client_details']);
        Route::post('/accept-client', [UserController::class, 'accept_client']);
        Route::post('/decline-client', [UserController::class, 'decline_client']);
    });
    // Cases

    Route::prefix('cases')->group(function () {

        Route::get('/show-all-cases', [StateController::class, 'index']);  // admin do this 😎
        Route::get('/show-client-cases/{client_id}', [StateController::class, 'show_client_cases']);  // admin and client do this 😎
        Route::post('/add', [StateController::class, 'add']); // admin and client do this 😎
        Route::get('/show-case-details/{case_id}', [StateController::class, 'show_case_details']);  // admin and client do this 😎
        Route::post('/request-cancellation', [StateController::class, 'delete_request']); // client do this 😎
        Route::post('/confirm-delivery', [StateController::class, 'confirm_delivery']); // client do this 😎
        Route::post('/change-status', [StateController::class, 'change_status']); // admin do this 😎

        Route::get('/download-case-image/{file_id}', [StateController::class, 'downloadFile']); // admin and client do this 😎

        // Search

        Route::post('/search', [StateController::class, 'search']); // admin and client do this 😎
        Route::post('/search-by-client-name', [StateController::class, 'search_by_client_name']); // admin and client do this 😎
        Route::post('/search-by-patient-name', [StateController::class, 'search_by_patient_name']); // admin and client do this 😎

        // Comments
        Route::prefix('comments')->group(function () {

            Route::get('/show-case-comments/{case_id}', [CommentController::class, 'index']); // admin and client do this 😎
            Route::post('/add-comment', [CommentController::class, 'store']); // admin and client do this 😎
            Route::get('/delete-comment/{comment_id}', [CommentController::class, 'destroy']); // admin and client do this 😎
        });
    });

    // Bills , Bill_Cases

    Route::prefix('bills')->group(function () {

        // Route::post('/increase-account', [AccountController::class, 'increase_account']); // admin do this 😎
        // Route::get('/show-account-history', [CommentController::class, 'show-account-history']); // admin and client do this 😎

        Route::get('/show-client-bills/{client_id}', [BillController::class, 'show_client_bills']); // admin and client do this 😎
        Route::get('/show-bill-details/{bill_id}', [BillController::class, 'show_bill_details']); // admin and client do this 😎
        Route::post('/add', [BillController::class, 'add_bill']); // admin and client do this 😎
        Route::get('/client-search-by-date/{date}', [BillController::class, 'client_search_by_date']); // client do this 😎

    });

    // Accounts

    Route::prefix('accounts')->group(function () {

        Route::get('/show-account-history-by-client-id/{client_id}', [AccountController::class, 'show_account_history']); // admin and client do this 😎

        Route::post('/increase-account', [AccountController::class, 'increase_account']); // admin do this 😎

    });

    // Inventory ( Items , Categories  , Subcategories , ItemHistory
    Route::middleware([IsAdmin::class])->group(function () {

        Route::prefix('inventory')->group(function () {

            Route::resource('/categories', CategoryController::class);

            Route::prefix('/sub-categories')->group(function () {

                Route::get('/show-subcategories-by-category-id/{category_id}', [SubcategoryController::class, 'index']); // admin do this 😎
                Route::get('/show-subcategory-details/{subcategory_id}', [SubcategoryController::class, 'show']); // admin and client do this 😎
                Route::post('/add', [SubcategoryController::class, 'store']); // admin and client do this 😎
                Route::put('/update-subcategory/{subcategory_id}', [SubcategoryController::class, 'update']); // admin and client do this 😎
                Route::delete('/delete-subcategory/{subcategory_id}', [SubcategoryController::class, 'destroy']); // admin and client do this 😎
            });

            Route::prefix('/items')->group(function () {

                Route::get('/show-all-items', [ItemController::class, 'index']); // admin do this 😎
                Route::get('/show-items-by-category-id/{category_id}', [ItemController::class, 'show_items_by_category_id']); // admin do this 😎
                Route::get('/show-items-by-subcategory-id/{subcategory_id}', [ItemController::class, 'show_items_by_subcategory_id']); // admin do this 😎
                Route::get('/show-details/{item_id}', [ItemController::class, 'show']); // admin and client do this 😎
                Route::post('/add', [ItemController::class, 'store']); // admin and client do this 😎
                Route::put('/update/{item_id}', [ItemController::class, 'update']); // admin and client do this 😎
                Route::delete('/delete-item/{item_id}', [ItemController::class, 'destroy']); // admin and client do this 😎
            });
        });
    });


    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/array', function (Request $request) {

    $string = $request->input('items');
    // $string = "1,2,3";
    $array = array_map('intval', explode(",", $string));

    return response()->json($array, 200);
});
