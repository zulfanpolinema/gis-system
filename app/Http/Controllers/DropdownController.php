<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinanceType;
use App\Models\User;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;
use Spatie\Permission\Models\Role;

class DropdownController extends Controller
{
    public function getRoles()
    {
        $roles = Role::select('id', 'name')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                'id' => $role->id,
                'text' => $role->name,
            ];
        }

        return response()->json(['results' => $data]);
    }

    public function getCategories()
    {
        $categories = Category::select('id', 'name as text')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get()->toArray();
        return response()->json(['results' => $categories]);
    }

    public function getFarmers()
    {
        $users = User::role('Petani')->select('id', 'name as text')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get()->toArray();
        return response()->json(['results' => $users]);
    }

    public function getProvinces()
    {
        $provinces = Province::select('code', 'name')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($provinces as $province) {
            $data[] = [
                'id'    => $province->code,
                'text'  => ucwords(strtolower($province->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }

    public function getCities()
    {
        $cities = City::select('code', 'name')
            ->where([
                ['province_code', request()->input('province_id', '')],
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($cities as $city) {
            $data[] = [
                'id'    => $city->code,
                'text'  => ucwords(strtolower($city->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }

    public function getSubdistricts()
    {
        $districts = District::select('code', 'name')
            ->where([
                ['city_code', request()->input('city_id', '')],
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($districts as $district) {
            $data[] = [
                'id'    => $district->code,
                'text'  => ucwords(strtolower($district->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }

    public function getVillages()
    {
        $villages = Village::select('id', 'name')
            ->where([
                ['district_code', request()->input('subdistrict_id', '')],
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($villages as $village) {
            $data[] = [
                'id'    => $village->id,
                'text'  => ucwords(strtolower($village->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }
}
