<?php

namespace App\Providers;

use App\Http\Security\Authentication;
use App\Http\Security\AuthenticationInterface;
use App\Models\Product;
use App\Models\Uom;
use App\Versions\V1\Services\Account\ForgotPassword;
use App\Versions\V1\Services\Account\ForgotPasswordInterface;
use App\Versions\V1\Services\Account\SettingsService;
use App\Versions\V1\Services\Account\SettingsServiceInterface;
use App\Versions\V1\Services\Account\Verification;
use App\Versions\V1\Services\Account\VerificationInterface;
use App\Versions\V1\Services\Branches\BranchService;
use App\Versions\V1\Services\Branches\BranchServiceInterface;
use App\Versions\V1\Services\Employees\EmployeeService;
use App\Versions\V1\Services\Employees\EmployeeServiceInterface;
use App\Versions\V1\Services\EntryPointService;
use App\Versions\V1\Services\EntryPointServiceInterface;
use App\Versions\V1\Services\ParkingService;
use App\Versions\V1\Services\ParkingServiceInterface;
use App\Versions\V1\Services\ParkingSlotService;
use App\Versions\V1\Services\ParkingSlotServiceInterface;
use App\Versions\V1\Services\Products\ProductCategoryService;
use App\Versions\V1\Services\Products\ProductCategoryServiceInterface;
use App\Versions\V1\Services\Products\ProductRequestService;
use App\Versions\V1\Services\Products\ProductRequestServiceInterface;
use App\Versions\V1\Services\Products\ProductService;
use App\Versions\V1\Services\Products\ProductServiceInterface;
use App\Versions\V1\Services\Products\ProductUomService;
use App\Versions\V1\Services\Products\ProductUomServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {


        $this->app->router->get("/", function(){
            return response()->json([
                "message" => "Access Denied"
            ]);
        });



    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        //Bindings
        $this->app->bind(ParkingSlotServiceInterface::class, ParkingSlotService::class);
        $this->app->bind(ParkingServiceInterface::class, ParkingService::class);

    }
}
