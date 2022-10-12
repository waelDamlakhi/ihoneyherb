<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Models\BannerProduct;
use App\Models\Product;
use App\Traits\GeneralFunctions;
use Exception;

class BannerController extends Controller
{
    use GeneralFunctions;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('tokenAuth:admin-api');
    }
    
    /**
     * Get All Products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts()
    {
        try 
        {
            $products = Product::select('id')->with(
                [
                    'translations' => function ($translation) 
                    {
                        $translation->select('name', 'product_id', 'locale');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, "These Are All Products", $products);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Banners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBanners()
    {
        try 
        {
            $banners = Banner::select('id', 'name')->get();
            return $this->makeResponse("Success", 200, "These Are All Banners", $banners);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }

    /**
     * Create Products Banner.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(BannerRequest $request)
    {
        try 
        {
            $banner = Banner::withCount(['products AS bannerCount'])->find($request->banner_id);
            if ($banner->count > $banner->bannerCount) 
            {
                $request->merge($this->uploadFiles($request->file('photo'), 'bannerUrl', 'bannerPath'));
                $banner->products()->syncWithoutDetaching(
                    [
                        $request->product_id => [
                            'bannerUrl' => $request->bannerUrl, 
                            'bannerPath' => $request->bannerPath, 
                            'admin_id' => $request->admin_id
                        ]
                    ]
                );
                return $this->makeResponse("Success", 200, "Product Banner Added Successfully");
            }
            return $this->makeResponse("Faild", 422, app()->getLocale() == 'en' ? 'There Is Not Enough Space In This Banner': 'لا توجد مساحة كافية في هذه اللافتة');
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get All Products Banners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        try 
        {
            $productsBanners = BannerProduct::with(
                [ 
                    'product' => function ($product) 
                    {
                        $product->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    },
                    'admin' => function ($admin) 
                    {
                        $admin->select('id', 'name');
                    },
                    'banner' => function ($banner)
                    {
                        $banner->select('id', 'name');
                    }
                ]
            )->get();
            return $this->makeResponse("Success", 200, "These Are All Products Banners", $productsBanners);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Get Product Banner Data For Edit It.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(BannerRequest $request)
    {
        try 
        {
            $productBanner = BannerProduct::with(
                [ 
                    'product' => function ($product) 
                    {
                        $product->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    },
                    'banner' => function ($banner)
                    {
                        $banner->select('id', 'name');
                    }
                ]
            )->find($request->id);
            return $this->makeResponse("Success", 200, "This Is Product Banner Data", $productBanner);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * Update Product Banner.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BannerRequest $request)
    {
        try 
        {
            $productBanner = BannerProduct::with(
                [ 
                    'product' => function ($product) 
                    {
                        $product->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    },
                    'banner' => function ($banner)
                    {
                        $banner->select('id', 'name');
                    }
                ]
            )->find($request->id);
            if ($productBanner->banner_id != $request->banner_id) 
            {
                $banner = Banner::withCount(['products AS bannerCount'])->find($request->banner_id);
                if ($banner->count == $banner->bannerCount) 
                {
                    return $this->makeResponse("Faild", 422, app()->getLocale() == 'en' ? 'There Is Not Enough Space In This Banner': 'لا توجد مساحة كافية في هذه اللافتة');
                }
            }
            if (!empty($request->file('photo'))) 
            {
                unlink($productBanner->bannerPath);
                $request->request->add($this->uploadFiles($request->file('photo'), 'bannerUrl', 'bannerPath'));
            }
            $productBanner->update($request->request->all());
            $productBanner = $productBanner->fresh(
                [ 
                    'product' => function ($product) 
                    {
                        $product->select('id')->with(
                            [
                                'translations' => function ($translation) 
                                {
                                    $translation->select('name', 'product_id', 'locale');
                                }
                            ]
                        );
                    },
                    'banner' => function ($banner)
                    {
                        $banner->select('id', 'name');
                    }
                ]
            );
            return $this->makeResponse("Success", 200, "Product Banner Updated Successfully", $productBanner);
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
    
    /**
     * delete Product Banner.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(BannerRequest $request)
    {
        try 
        {
            $productBanner = BannerProduct::find($request->id);
            unlink($productBanner->bannerPath);
            $productBanner->delete();
            return $this->makeResponse("Success", 200, "Product Banner Deleted Successfully");
        }
        catch (Exception $e) 
        {
            return $this->makeResponse("Faild", $e->getCode(), $e->getmessage());
        }
    }
}
